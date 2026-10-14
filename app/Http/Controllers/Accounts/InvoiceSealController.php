<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceSealController extends Controller
{
    /**
     * Retrieve the public URL of the uploaded custom seal image if it exists.
     */
    public static function getCustomSealUrl(): ?string
    {
        $dir = public_path('uploads/invoice_seals');
        if (!file_exists($dir)) {
            return null;
        }

        $matches = glob($dir . '/custom_seal.*');
        if (!empty($matches)) {
            $filename = basename($matches[0]);
            return asset('uploads/invoice_seals/' . $filename) . '?v=' . filemtime($matches[0]);
        }

        return null;
    }

    /**
     * Upload and persist a custom seal & signature image.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'seal_image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $file = $request->file('seal_image');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = 'custom_seal.' . $ext;

        $dir = public_path('uploads/invoice_seals');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        // Clean up any existing custom_seal files to avoid mismatched extensions
        foreach (glob($dir . '/custom_seal.*') as $old) {
            @unlink($old);
        }

        $file->move($dir, $filename);

        $url = asset('uploads/invoice_seals/' . $filename) . '?v=' . time();

        return response()->json([
            'success' => true,
            'url'     => $url,
            'message' => 'Custom seal and signature uploaded successfully.',
        ]);
    }

    /**
     * Remove the custom seal image, reverting all invoices to blank white space.
     */
    public function remove(): JsonResponse
    {
        $dir = public_path('uploads/invoice_seals');
        if (file_exists($dir)) {
            foreach (glob($dir . '/custom_seal.*') as $file) {
                @unlink($file);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Custom seal removed. Invoices will show blank white space for manual stamping and signing.',
        ]);
    }

    /**
     * Check the current seal status.
     */
    public function status(): JsonResponse
    {
        $url = self::getCustomSealUrl();
        return response()->json([
            'has_seal' => !empty($url),
            'url'      => $url,
        ]);
    }
}
