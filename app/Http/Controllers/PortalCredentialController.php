<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\EcCertificate;
use App\Models\EnvironmentProject;
use App\Models\PptApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Returns saved Parivesh / Email portal passwords on demand.
 *
 * Pages no longer print these passwords into their HTML (wizard dropdowns used
 * to carry every customer's passwords). Show / Copy buttons and wizard
 * autofill call this endpoint for one record at a time; each call is logged.
 */
class PortalCredentialController extends Controller
{
    /** type => [model class, permissions that may read it] */
    private const TYPES = [
        'customer'       => [Customer::class, ['environment.create', 'environment.edit', 'ppt.create', 'ppt.edit', 'ec_certificate.create', 'ec_certificate.edit']],
        'environment'    => [EnvironmentProject::class, ['environment.view']],
        'ppt'            => [PptApplication::class, ['ppt.view']],
        'ec_certificate' => [EcCertificate::class, ['ec_certificate.view']],
    ];

    public function show(Request $request, string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        [$class, $permissions] = self::TYPES[$type];
        abort_unless($request->user() && $request->user()->canAny($permissions), 403);

        $record = $class::findOrFail($id);
        [$parivesh, $email] = $this->resolve($type, $record);

        if ($parivesh || $email) {
            try {
                ActivityLog::create([
                    'user_id'       => Auth::id(),
                    'action'        => 'portal_credentials_viewed',
                    'description'   => 'Portal passwords revealed for ' . $type . ' #' . $record->id . ' by ' . (Auth::user()->name ?? 'user') . '.',
                    'loggable_type' => $class,
                    'loggable_id'   => $record->id,
                    'ip_address'    => $request->ip(),
                    'user_agent'    => $request->userAgent(),
                    'created_at'    => now(),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Credential view log failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status'            => 1,
            'parivesh_password' => $parivesh ?: '',
            'email_password'    => $email ?: '',
        ])->header('Cache-Control', 'no-store');
    }

    /**
     * Own passwords first, then the linked environment project, then the customer
     * (same fallback order the pages used before).
     */
    private function resolve(string $type, $record): array
    {
        $parivesh = $record->parivesh_password ?? null;
        $email = $record->email_password ?? null;

        $fallbacks = [];
        if ($type === 'ec_certificate' || $type === 'ppt') {
            $fallbacks[] = $record->environmentProject ?? null;
        }
        if ($type !== 'customer') {
            $fallbacks[] = $record->customer ?? null;
            $fallbacks[] = $record->environmentProject?->customer ?? null;
        }
        foreach ($fallbacks as $source) {
            if (!$source) {
                continue;
            }
            $parivesh = $parivesh ?: ($source->parivesh_password ?? null);
            $email = $email ?: ($source->email_password ?? null);
        }

        return [$parivesh, $email];
    }
}
