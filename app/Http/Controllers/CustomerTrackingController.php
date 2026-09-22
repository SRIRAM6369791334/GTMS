<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EcCertificate;
use App\Models\EnvironmentProject;
use App\Models\LeaseApplication;
use App\Models\MiningApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerTrackingController extends Controller
{
    /**
     * Display the Customer 360 Tracking Portal
     */
    public function index(Request $request)
    {
        $query = trim($request->input('q', ''));
        $customer = null;

        if (!empty($query)) {
            $customer = $this->findCustomerByUniversalQuery($query);
        }

        // Global KPI Stats for the Tracking Dashboard
        $stats = [
            'total_customers'  => Customer::count(),
            'total_leases'     => LeaseApplication::count(),
            'total_mining'     => MiningApplication::count(),
            'total_env'        => EnvironmentProject::count(),
            'total_ec_certs'   => EcCertificate::count(),
        ];

        // Recent Active Customers for quick-select cards
        $recentCustomers = Customer::with(['district', 'mineral'])
            ->withCount(['leaseApplications', 'miningApplications', 'environmentProjects', 'ecCertificates'])
            ->latest()
            ->take(6)
            ->get();

        $dossierData = null;
        if ($customer) {
            $dossierData = $this->buildCustomerDossier($customer);
        }

        return view('pages.customer_tracking.index', compact(
            'customer',
            'query',
            'stats',
            'recentCustomers',
            'dossierData'
        ));
    }

    /**
     * Show tracking dossier for a specific customer
     */
    public function show($customer)
    {
        if (!$customer instanceof Customer) {
            $cleanDigits = preg_replace('/[^0-9]/', '', (string)$customer);
            $customer = Customer::where('slug', $customer)
                ->orWhere('id', $customer)
                ->orWhere('mimas_no', $customer)
                ->when(strlen($cleanDigits) >= 8, function ($q) use ($cleanDigits, $customer) {
                    $q->orWhere('aadhaar_no', $customer)
                      ->orWhereRaw("REPLACE(REPLACE(COALESCE(aadhaar_no, ''), '-', ''), ' ', '') = ?", [$cleanDigits]);
                })
                ->firstOrFail();
        }
        $stats = [
            'total_customers'  => Customer::count(),
            'total_leases'     => LeaseApplication::count(),
            'total_mining'     => MiningApplication::count(),
            'total_env'        => EnvironmentProject::count(),
            'total_ec_certs'   => EcCertificate::count(),
        ];

        $recentCustomers = Customer::with(['district', 'mineral'])
            ->withCount(['leaseApplications', 'miningApplications', 'environmentProjects', 'ecCertificates'])
            ->latest()
            ->take(6)
            ->get();

        $dossierData = $this->buildCustomerDossier($customer);
        $query = $customer->mimas_no ?: $customer->customer_name;

        return view('pages.customer_tracking.index', compact(
            'customer',
            'query',
            'stats',
            'recentCustomers',
            'dossierData'
        ));
    }

    /**
     * AJAX Live Autocomplete Search Endpoint
     */
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $cleanDigits = preg_replace('/[^0-9]/', '', $q);
        $cleanAlphanumeric = preg_replace('/[^a-zA-Z0-9]/', '', $q);

        $customers = Customer::with(['district', 'mineral'])
            ->where(function ($query) use ($q, $cleanDigits, $cleanAlphanumeric) {
                $query->where('customer_name', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%")
                    ->orWhere('mimas_no', 'like', "%{$q}%")
                    ->orWhere('aadhaar_no', 'like', "%{$q}%")
                    ->orWhere('mobile_num', 'like', "%{$q}%")
                    ->orWhere('secondary_mobile_num', 'like', "%{$q}%")
                    ->orWhere('pan', 'like', "%{$q}%");

                // Normalized Aadhaar search (e.g. 323268689898, 3232-6868-9898, 3232 6868 9898, or partial 32326868)
                if (!empty($cleanDigits) && strlen($cleanDigits) >= 4) {
                    $query->orWhereRaw("REPLACE(REPLACE(COALESCE(aadhaar_no, ''), '-', ''), ' ', '') LIKE ?", ["%{$cleanDigits}%"]);

                    if (strlen($cleanDigits) === 12) {
                        $dashed = substr($cleanDigits, 0, 4) . '-' . substr($cleanDigits, 4, 4) . '-' . substr($cleanDigits, 8, 4);
                        $spaced = substr($cleanDigits, 0, 4) . ' ' . substr($cleanDigits, 4, 4) . ' ' . substr($cleanDigits, 8, 4);
                        $query->orWhere('aadhaar_no', $dashed)
                              ->orWhere('aadhaar_no', $spaced)
                              ->orWhere('aadhaar_no', $cleanDigits);
                    }
                }

                // Normalized Mobile search (+91, spaces, dashes)
                if (!empty($cleanDigits) && strlen($cleanDigits) >= 5) {
                    $query->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(mobile_num, ''), '-', ''), ' ', ''), '+91', '') LIKE ?", ["%{$cleanDigits}%"])
                          ->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(secondary_mobile_num, ''), '-', ''), ' ', ''), '+91', '') LIKE ?", ["%{$cleanDigits}%"]);

                    if (strlen($cleanDigits) >= 10) {
                        $last10 = substr($cleanDigits, -10);
                        $query->orWhere('mobile_num', 'like', "%{$last10}%")
                              ->orWhere('secondary_mobile_num', 'like', "%{$last10}%");
                    }
                }

                // Normalized MIMAS / PAN
                if (!empty($cleanAlphanumeric) && strlen($cleanAlphanumeric) >= 3) {
                    $query->orWhereRaw("REPLACE(REPLACE(COALESCE(mimas_no, ''), '-', ''), ' ', '') LIKE ?", ["%{$cleanAlphanumeric}%"])
                          ->orWhereRaw("REPLACE(REPLACE(COALESCE(pan, ''), '-', ''), ' ', '') LIKE ?", ["%{$cleanAlphanumeric}%"]);
                }

                // Related Applications matching
                $query->orWhereHas('leaseApplications', function ($lq) use ($q) {
                    $lq->where('application_no', 'like', "%{$q}%")
                        ->orWhere('common_id', 'like', "%{$q}%");
                })
                ->orWhereHas('miningApplications', function ($mq) use ($q) {
                    $mq->where('application_no', 'like', "%{$q}%")
                        ->orWhere('common_id', 'like', "%{$q}%");
                })
                ->orWhereHas('environmentProjects', function ($eq) use ($q) {
                    $eq->where('project_code', 'like', "%{$q}%");
                })
                ->orWhereHas('ecCertificates', function ($cq) use ($q) {
                    $cq->where('ec_ref_no', 'like', "%{$q}%")
                        ->orWhere('parivesh_app_no', 'like', "%{$q}%");
                });
            })
            ->take(8)
            ->get();

        $results = $customers->map(function ($c) {
            // Determine active stage
            $stage = 'Profile Registered';
            if ($c->ecCertificates()->exists()) {
                $stage = 'EC Certificate Issued';
            } elseif ($c->environmentProjects()->exists()) {
                $stage = 'Environment Clearance';
            } elseif ($c->miningApplications()->exists()) {
                $stage = 'Mining Plan';
            } elseif ($c->leaseApplications()->exists()) {
                $stage = 'Lease Application';
            }

            $maskedAadhaar = null;
            if ($c->aadhaar_no) {
                $cleanAadhaar = preg_replace('/[^0-9]/', '', $c->aadhaar_no);
                if (strlen($cleanAadhaar) >= 8) {
                    $maskedAadhaar = substr($cleanAadhaar, 0, 4) . ' **** ' . substr($cleanAadhaar, -4);
                } else {
                    $maskedAadhaar = $c->aadhaar_no;
                }
            }

            return [
                'id'           => $c->id,
                'name'         => $c->customer_name,
                'company'      => $c->company_name ?: 'Individual Applicant',
                'mimas_no'     => $c->mimas_no,
                'mobile'       => $c->mobile_num,
                'aadhaar'      => $maskedAadhaar,
                'district'     => $c->district?->name ?: 'N/A',
                'active_stage' => $stage,
                'url'          => route('customer-tracking.show', $c->slug ?? $c->id),
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Resolve Customer record by universal keyword
     */
    private function findCustomerByUniversalQuery(string $q): ?Customer
    {
        $q = trim($q);
        $cleanDigits = preg_replace('/[^0-9]/', '', $q);
        $cleanAlphanumeric = preg_replace('/[^a-zA-Z0-9]/', '', $q);

        // 1. Direct ID match (only for short IDs <= 6 digits, to avoid 10-digit mobile or 12-digit Aadhaar being queried as ID)
        if (is_numeric($q) && strlen($q) <= 6) {
            $c = Customer::find($q);
            if ($c) return $c;
        }

        // 2. Aadhaar match: handles raw, plain digits (e.g. 323268689898), dashed (3232-6868-9898), or spaced
        if (!empty($cleanDigits) && strlen($cleanDigits) >= 8) {
            $cAadhaar = Customer::where(function ($aq) use ($q, $cleanDigits) {
                $aq->where('aadhaar_no', $q)
                   ->orWhere('aadhaar_no', $cleanDigits)
                   ->orWhereRaw("REPLACE(REPLACE(COALESCE(aadhaar_no, ''), '-', ''), ' ', '') = ?", [$cleanDigits]);

                if (strlen($cleanDigits) === 12) {
                    $dashed = substr($cleanDigits, 0, 4) . '-' . substr($cleanDigits, 4, 4) . '-' . substr($cleanDigits, 8, 4);
                    $spaced = substr($cleanDigits, 0, 4) . ' ' . substr($cleanDigits, 4, 4) . ' ' . substr($cleanDigits, 8, 4);
                    $aq->orWhere('aadhaar_no', $dashed)
                       ->orWhere('aadhaar_no', $spaced);
                }
            })->first();

            if ($cAadhaar) return $cAadhaar;
        }

        // 3. Mobile match: handles 10 digits, +91, leading 0, or formatted numbers
        if (!empty($cleanDigits) && strlen($cleanDigits) >= 10) {
            $last10 = substr($cleanDigits, -10);
            $cMobile = Customer::where('mobile_num', $q)
                ->orWhere('mobile_num', $cleanDigits)
                ->orWhere('mobile_num', $last10)
                ->orWhere('secondary_mobile_num', $last10)
                ->orWhereRaw("RIGHT(REPLACE(REPLACE(REPLACE(COALESCE(mobile_num, ''), '-', ''), ' ', ''), '+91', ''), 10) = ?", [$last10])
                ->first();
            if ($cMobile) return $cMobile;
        }

        // 4. MIMAS, PAN, or exact raw match
        $c = Customer::where('mimas_no', $q)
            ->orWhere('pan', $q)
            ->first();
        if ($c) return $c;

        if (!empty($cleanAlphanumeric)) {
            $cMimas = Customer::whereRaw("REPLACE(REPLACE(COALESCE(mimas_no, ''), '-', ''), ' ', '') = ?", [$cleanAlphanumeric])
                ->orWhereRaw("REPLACE(REPLACE(COALESCE(pan, ''), '-', ''), ' ', '') = ?", [$cleanAlphanumeric])
                ->first();
            if ($cMimas) return $cMimas;
        }

        // 5. Match via Application No / Common ID
        $lease = LeaseApplication::where('application_no', $q)->orWhere('common_id', $q)->first();
        if ($lease && $lease->customer) return $lease->customer;

        $mining = MiningApplication::where('application_no', $q)->orWhere('common_id', $q)->first();
        if ($mining && $mining->customer) return $mining->customer;

        $env = EnvironmentProject::where('project_code', $q)->first();
        if ($env && $env->customer) return $env->customer;

        $cert = EcCertificate::where('ec_ref_no', $q)->orWhere('parivesh_app_no', $q)->first();
        if ($cert && $cert->customer) return $cert->customer;

        // 6. Fuzzy search fallback by name, company, mimas, mobile, or aadhaar
        return Customer::where('customer_name', 'like', "%{$q}%")
            ->orWhere('company_name', 'like', "%{$q}%")
            ->orWhere('mimas_no', 'like', "%{$q}%")
            ->orWhere('mobile_num', 'like', "%{$q}%")
            ->orWhere('aadhaar_no', 'like', "%{$q}%")
            ->first();
    }

    /**
     * Build comprehensive 360-degree Dossier Data for Customer
     */
    private function buildCustomerDossier(Customer $customer): array
    {
        $customer->load([
            'district',
            'mineral',
            'leaseApplications.district',
            'leaseApplications.mineral',
            'leaseApplications.documents.folder',
            'leaseApplications.surveyNumbers',
            'miningApplications.documents.folder',
            'miningApplications.natureOfWork',
            'miningApplications.planType',
            'environmentProjects.documents.folder',
            'ecCertificates.environmentProject',
            'pptApplications',
            'dgpsSurveys',
            'droneSurveys',
        ]);

        $leaseApp = $customer->leaseApplications->first();
        $miningApp = $customer->miningApplications->first();
        $envProj = $customer->environmentProjects->first();
        $ecCert = $customer->ecCertificates->first();

        // Calculate Lifecycle Stepper Status
        $stepper = [
            1 => [
                'name'        => 'Lease Application',
                'status'      => $leaseApp ? ($leaseApp->status === 'approved' ? 'completed' : 'in_progress') : 'pending',
                'app_no'      => $leaseApp?->application_no ?: 'Not started',
                'date'        => $leaseApp?->created_at ? $leaseApp->created_at->format('d M Y') : null,
                'badge_color' => $leaseApp ? ($leaseApp->status === 'approved' ? 'success' : 'warning') : 'secondary',
            ],
            2 => [
                'name'        => 'Mining Plan',
                'status'      => $miningApp ? ($miningApp->status === 'approved' ? 'completed' : 'in_progress') : 'pending',
                'app_no'      => $miningApp?->application_no ?: 'Not started',
                'date'        => $miningApp?->created_at ? $miningApp->created_at->format('d M Y') : null,
                'badge_color' => $miningApp ? ($miningApp->status === 'approved' ? 'success' : 'warning') : 'secondary',
            ],
            3 => [
                'name'        => 'Environment Clearance',
                'status'      => $envProj ? ($envProj->status === 'approved' ? 'completed' : 'in_progress') : 'pending',
                'app_no'      => $envProj?->project_code ?: 'Not started',
                'date'        => $envProj?->created_at ? $envProj->created_at->format('d M Y') : null,
                'badge_color' => $envProj ? ($envProj->status === 'approved' ? 'success' : 'warning') : 'secondary',
            ],
            4 => [
                'name'        => 'EC Certificate',
                'status'      => $ecCert ? 'completed' : 'pending',
                'app_no'      => $ecCert?->ec_ref_no ?: 'Not issued',
                'date'        => $ecCert?->issue_date ? $ecCert->issue_date->format('d M Y') : null,
                'badge_color' => $ecCert ? 'success' : 'secondary',
            ],
            5 => [
                'name'        => 'Mine Opening & PPT',
                'status'      => $customer->pptApplications->isNotEmpty() ? 'in_progress' : ($ecCert ? 'ready' : 'pending'),
                'app_no'      => $customer->pptApplications->first()?->application_no ?: 'Awaiting EC',
                'date'        => null,
                'badge_color' => $customer->pptApplications->isNotEmpty() ? 'info' : ($ecCert ? 'warning' : 'secondary'),
            ],
        ];

        // Calculate Overall Progress (0% to 100%)
        $completedSteps = collect($stepper)->filter(fn($s) => $s['status'] === 'completed')->count();
        $inProgressSteps = collect($stepper)->filter(fn($s) => $s['status'] === 'in_progress')->count();
        $progressPercent = min(100, round(($completedSteps * 20) + ($inProgressSteps * 10)));

        // Consolidated Document Vault
        $allDocuments = collect();

        // 1. Lease Documents
        if ($leaseApp) {
            foreach ($leaseApp->documents as $doc) {
                if ($doc->file_path) {
                    $allDocuments->push([
                        'module'      => 'Lease Application',
                        'module_code' => 'lease',
                        'folder'      => $doc->folder?->name ?: 'Regulatory Docs',
                        'name'        => $doc->document_name,
                        'file_name'   => $doc->file_name,
                        'file_path'   => $doc->file_path,
                        'file_size'   => $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : '—',
                        'status'      => $doc->status,
                        'date'        => $doc->uploaded_at ? $doc->uploaded_at->format('d M Y') : 'Saved',
                    ]);
                }
            }
        }

        // 2. Mining Documents
        if ($miningApp) {
            foreach ($miningApp->documents as $doc) {
                if ($doc->file_path) {
                    $allDocuments->push([
                        'module'      => 'Mining Plan',
                        'module_code' => 'mining',
                        'folder'      => $doc->folder?->name ?: 'Plan Documents',
                        'name'        => $doc->document_name,
                        'file_name'   => $doc->file_name,
                        'file_path'   => $doc->file_path,
                        'file_size'   => $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : '—',
                        'status'      => $doc->status,
                        'date'        => $doc->uploaded_at ? $doc->uploaded_at->format('d M Y') : 'Saved',
                    ]);
                }
            }
        }

        // 3. Environment Documents
        if ($envProj) {
            foreach ($envProj->documents as $doc) {
                if ($doc->file_path) {
                    $allDocuments->push([
                        'module'      => 'Environment Clearance',
                        'module_code' => 'environment',
                        'folder'      => $doc->folder?->name ?: 'EC Folder',
                        'name'        => $doc->document_name,
                        'file_name'   => $doc->file_name,
                        'file_path'   => $doc->file_path,
                        'file_size'   => $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : '—',
                        'status'      => $doc->status,
                        'date'        => $doc->uploaded_at ? $doc->uploaded_at->format('d M Y') : 'Saved',
                    ]);
                }
            }
        }

        // 4. EC Certificate File
        if ($ecCert && $ecCert->certificate_file) {
            $allDocuments->push([
                'module'      => 'EC Certificate',
                'module_code' => 'ec',
                'folder'      => 'Official EC Certificate',
                'name'        => 'Official Environmental Clearance Certificate',
                'file_name'   => basename($ecCert->certificate_file),
                'file_path'   => $ecCert->certificate_file,
                'file_size'   => file_exists(public_path($ecCert->certificate_file)) ? number_format(filesize(public_path($ecCert->certificate_file)) / 1024, 1) . ' KB' : '—',
                'status'      => 'active',
                'date'        => $ecCert->issue_date ? $ecCert->issue_date->format('d M Y') : 'Issued',
            ]);
        }

        // EC Validity Remaining calculation
        $ecValidity = null;
        if ($ecCert && $ecCert->expiry_date) {
            $now = Carbon::now();
            $expiry = Carbon::parse($ecCert->expiry_date);
            if ($now->greaterThan($expiry)) {
                $ecValidity = ['text' => 'Expired on ' . $expiry->format('d M Y'), 'badge' => 'danger', 'is_expired' => true];
            } else {
                $years = $now->diffInYears($expiry);
                $months = $now->copy()->addYears($years)->diffInMonths($expiry);
                $ecValidity = [
                    'text'       => "Valid until {$expiry->format('d M Y')} ({$years}y {$months}m remaining)",
                    'badge'      => 'success',
                    'is_expired' => false,
                ];
            }
        }

        return [
            'stepper'         => $stepper,
            'progressPercent' => $progressPercent,
            'leaseApp'        => $leaseApp,
            'miningApp'       => $miningApp,
            'envProj'         => $envProj,
            'ecCert'          => $ecCert,
            'ecValidity'      => $ecValidity,
            'allDocuments'    => $allDocuments,
        ];
    }
}
