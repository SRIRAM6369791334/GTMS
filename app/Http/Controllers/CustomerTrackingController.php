<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\District;
use App\Models\EcCertificate;
use App\Models\EnvironmentProject;
use App\Models\LeaseApplication;
use App\Models\MiningApplication;
use App\Models\PptApplication;
use App\Models\DgpsSurvey;
use App\Models\DroneSurvey;
use App\Models\EcCompliance;
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
        $districtId = $request->input('district_id');
        $appType = $request->input('app_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $isFiltered = !empty($query) || !empty($districtId) || !empty($appType) || !empty($dateFrom) || !empty($dateTo);
        $customer = null;

        // If a direct keyword lookup was submitted without conflicting multi-select filters, test for direct customer match
        if (!empty($query) && empty($districtId) && empty($appType) && empty($dateFrom) && empty($dateTo)) {
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

        // All districts for dropdown
        $districts = District::orderBy('name')->get();

        // Application types for dropdown
        $appTypes = [
            'lease'          => 'Lease Application',
            'mining'         => 'Mining Plan',
            'environment'    => 'Environment Clearance (B1 / B2)',
            'ec_certificate' => 'EC Certificate',
            'ppt'            => 'PPT Department',
            'dgps'           => 'DGPS Land Survey',
            'drone'          => 'Drone Volumetric Survey',
            'ec_compliance'  => 'EC Half-Yearly Compliance',
        ];

        // Query customers list with filters
        $customersQuery = Customer::with(['district', 'mineral'])
            ->withCount([
                'leaseApplications',
                'miningApplications',
                'environmentProjects',
                'ecCertificates',
                'pptApplications',
                'dgpsSurveys',
                'droneSurveys',
                'ecCompliances',
            ]);

        // 1. Text Query Filter (Customer Unique ID, Name, Aadhaar, PAN, All Phone Numbers, Quarry Name, App No)
        if (!empty($query)) {
            $cleanDigits = preg_replace('/[^0-9]/', '', $query);
            $cleanAlphanumeric = preg_replace('/[^a-zA-Z0-9]/', '', $query);

            $customersQuery->where(function ($subQ) use ($query, $cleanDigits, $cleanAlphanumeric) {
                $subQ->where('customer_name', 'like', "%{$query}%")
                    ->orWhere('company_name', 'like', "%{$query}%")
                    ->orWhere('mimas_no', 'like', "%{$query}%")
                    ->orWhere('aadhaar_no', 'like', "%{$query}%")
                    ->orWhere('pan', 'like', "%{$query}%")
                    ->orWhere('mobile_num', 'like', "%{$query}%")
                    ->orWhere('secondary_mobile_num', 'like', "%{$query}%");

                if (!empty($cleanDigits) && strlen($cleanDigits) >= 4) {
                    $subQ->orWhereRaw("REPLACE(REPLACE(COALESCE(aadhaar_no, ''), '-', ''), ' ', '') LIKE ?", ["%{$cleanDigits}%"]);
                    if (strlen($cleanDigits) === 12) {
                        $dashed = substr($cleanDigits, 0, 4) . '-' . substr($cleanDigits, 4, 4) . '-' . substr($cleanDigits, 8, 4);
                        $spaced = substr($cleanDigits, 0, 4) . ' ' . substr($cleanDigits, 4, 4) . ' ' . substr($cleanDigits, 8, 4);
                        $subQ->orWhere('aadhaar_no', $dashed)->orWhere('aadhaar_no', $spaced);
                    }
                }

                if (!empty($cleanDigits) && strlen($cleanDigits) >= 5) {
                    $subQ->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(mobile_num, ''), '-', ''), ' ', ''), '+91', '') LIKE ?", ["%{$cleanDigits}%"])
                        ->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(secondary_mobile_num, ''), '-', ''), ' ', ''), '+91', '') LIKE ?", ["%{$cleanDigits}%"]);
                    if (strlen($cleanDigits) >= 10) {
                        $last10 = substr($cleanDigits, -10);
                        $subQ->orWhere('mobile_num', 'like', "%{$last10}%")
                            ->orWhere('secondary_mobile_num', 'like', "%{$last10}%");
                    }
                }

                if (!empty($cleanAlphanumeric) && strlen($cleanAlphanumeric) >= 3) {
                    $subQ->orWhereRaw("REPLACE(REPLACE(COALESCE(mimas_no, ''), '-', ''), ' ', '') LIKE ?", ["%{$cleanAlphanumeric}%"]);
                }

                $subQ->orWhereHas('leaseApplications', function ($lq) use ($query) {
                    $lq->where('application_no', 'like', "%{$query}%")
                        ->orWhere('common_id', 'like', "%{$query}%")
                        ->orWhere('village', 'like', "%{$query}%")
                        ->orWhere('taluk', 'like', "%{$query}%")
                        ->orWhere('other_mineral_name', 'like', "%{$query}%");
                })
                ->orWhereHas('miningApplications', function ($mq) use ($query) {
                    $mq->where('application_no', 'like', "%{$query}%")
                        ->orWhere('common_id', 'like', "%{$query}%")
                        ->orWhere('village', 'like', "%{$query}%")
                        ->orWhere('taluk', 'like', "%{$query}%")
                        ->orWhere('other_mineral_name', 'like', "%{$query}%");
                })
                ->orWhereHas('environmentProjects', function ($eq) use ($query) {
                    $eq->where('project_code', 'like', "%{$query}%")
                        ->orWhere('project_name', 'like', "%{$query}%")
                        ->orWhere('location', 'like', "%{$query}%");
                })
                ->orWhereHas('ecCertificates', function ($cq) use ($query) {
                    $cq->where('ec_ref_no', 'like', "%{$query}%")
                        ->orWhere('parivesh_app_no', 'like', "%{$query}%")
                        ->orWhere('applicant_name', 'like', "%{$query}%");
                })
                ->orWhereHas('pptApplications', function ($pq) use ($query) {
                    $pq->where('application_no', 'like', "%{$query}%")
                        ->orWhere('project_name', 'like', "%{$query}%");
                })
                ->orWhereHas('dgpsSurveys', function ($dq) use ($query) {
                    $dq->where('survey_no', 'like', "%{$query}%")
                        ->orWhere('location', 'like', "%{$query}%");
                })
                ->orWhereHas('droneSurveys', function ($drq) use ($query) {
                    $drq->where('survey_no', 'like', "%{$query}%")
                        ->orWhere('location', 'like', "%{$query}%");
                })
                ->orWhereHas('ecCompliances', function ($ecq) use ($query) {
                    $ecq->where('compliance_no', 'like', "%{$query}%")
                        ->orWhere('project_name', 'like', "%{$query}%");
                });
            });
        }

        // 2. District Filter
        if (!empty($districtId)) {
            $customersQuery->where(function ($dq) use ($districtId) {
                $dq->where('district_id', $districtId)
                    ->orWhereHas('leaseApplications', fn($l) => $l->where('district_id', $districtId))
                    ->orWhereHas('miningApplications', fn($m) => $m->where('district_id', $districtId))
                    ->orWhereHas('environmentProjects', fn($e) => $e->where('district_id', $districtId));
            });
        }

        // 3. Application Type Filter
        if (!empty($appType)) {
            switch ($appType) {
                case 'lease':
                    $customersQuery->whereHas('leaseApplications');
                    break;
                case 'mining':
                    $customersQuery->whereHas('miningApplications');
                    break;
                case 'environment':
                    $customersQuery->whereHas('environmentProjects');
                    break;
                case 'ec_certificate':
                    $customersQuery->whereHas('ecCertificates');
                    break;
                case 'ppt':
                    $customersQuery->whereHas('pptApplications');
                    break;
                case 'dgps':
                    $customersQuery->whereHas('dgpsSurveys');
                    break;
                case 'drone':
                    $customersQuery->whereHas('droneSurveys');
                    break;
                case 'ec_compliance':
                    $customersQuery->whereHas('ecCompliances');
                    break;
            }
        }

        // 4. Date-wise Filter (application created time)
        if (!empty($dateFrom) || !empty($dateTo)) {
            $fromDate = !empty($dateFrom) ? Carbon::parse($dateFrom)->startOfDay() : null;
            $toDate = !empty($dateTo) ? Carbon::parse($dateTo)->endOfDay() : null;

            $applyDateRange = function ($builder) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $builder->whereBetween('created_at', [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $builder->where('created_at', '>=', $fromDate);
                } elseif ($toDate) {
                    $builder->where('created_at', '<=', $toDate);
                }
            };

            if (!empty($appType)) {
                switch ($appType) {
                    case 'lease':
                        $customersQuery->whereHas('leaseApplications', $applyDateRange);
                        break;
                    case 'mining':
                        $customersQuery->whereHas('miningApplications', $applyDateRange);
                        break;
                    case 'environment':
                        $customersQuery->whereHas('environmentProjects', $applyDateRange);
                        break;
                    case 'ec_certificate':
                        $customersQuery->whereHas('ecCertificates', $applyDateRange);
                        break;
                    case 'ppt':
                        $customersQuery->whereHas('pptApplications', $applyDateRange);
                        break;
                    case 'dgps':
                        $customersQuery->whereHas('dgpsSurveys', $applyDateRange);
                        break;
                    case 'drone':
                        $customersQuery->whereHas('droneSurveys', $applyDateRange);
                        break;
                    case 'ec_compliance':
                        $customersQuery->whereHas('ecCompliances', $applyDateRange);
                        break;
                }
            } else {
                $customersQuery->where(function ($sub) use ($applyDateRange) {
                    $applyDateRange($sub);
                    $sub->orWhereHas('leaseApplications', $applyDateRange)
                        ->orWhereHas('miningApplications', $applyDateRange)
                        ->orWhereHas('environmentProjects', $applyDateRange)
                        ->orWhereHas('ecCertificates', $applyDateRange)
                        ->orWhereHas('pptApplications', $applyDateRange)
                        ->orWhereHas('dgpsSurveys', $applyDateRange)
                        ->orWhereHas('droneSurveys', $applyDateRange)
                        ->orWhereHas('ecCompliances', $applyDateRange);
                });
            }
        }

        // Fetch matching customer list (paginate if filtered, or latest 6 if initial landing)
        $limit = $isFiltered ? 12 : 6;
        $customersList = $customersQuery->latest()->paginate($limit)->withQueryString();
        $recentCustomers = $customersList;

        // If only 1 customer found in a filtered search, or user searched a specific ID, build dossier
        if (!$customer && $isFiltered && $customersList->total() === 1 && !empty($query)) {
            $customer = $customersList->items()[0];
        }

        $dossierData = null;
        $customerAppsCatalog = collect();
        if ($customer) {
            $dossierData = $this->buildCustomerDossier($customer);
            $customerAppsCatalog = $this->getCustomerApplicationsCatalog($customer);
        }

        return view('pages.customer_tracking.index', compact(
            'customer',
            'query',
            'districtId',
            'appType',
            'dateFrom',
            'dateTo',
            'districts',
            'appTypes',
            'stats',
            'customersList',
            'recentCustomers',
            'isFiltered',
            'dossierData',
            'customerAppsCatalog'
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

        $districts = District::orderBy('name')->get();
        $appTypes = [
            'lease'          => 'Lease Application',
            'mining'         => 'Mining Plan',
            'environment'    => 'Environment Clearance (B1 / B2)',
            'ec_certificate' => 'EC Certificate',
            'ppt'            => 'PPT Department',
            'dgps'           => 'DGPS Land Survey',
            'drone'          => 'Drone Volumetric Survey',
            'ec_compliance'  => 'EC Half-Yearly Compliance',
        ];

        $isFiltered = false;
        $districtId = null;
        $appType = null;
        $dateFrom = null;
        $dateTo = null;

        $customersList = Customer::with(['district', 'mineral'])
            ->withCount(['leaseApplications', 'miningApplications', 'environmentProjects', 'ecCertificates', 'pptApplications', 'dgpsSurveys', 'droneSurveys', 'ecCompliances'])
            ->latest()
            ->paginate(6);
        $recentCustomers = $customersList;

        $dossierData = $this->buildCustomerDossier($customer);
        $customerAppsCatalog = $this->getCustomerApplicationsCatalog($customer);
        $query = $customer->mimas_no ?: $customer->customer_name;

        return view('pages.customer_tracking.index', compact(
            'customer',
            'query',
            'districtId',
            'appType',
            'dateFrom',
            'dateTo',
            'districts',
            'appTypes',
            'stats',
            'customersList',
            'recentCustomers',
            'isFiltered',
            'dossierData',
            'customerAppsCatalog'
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

                // Normalized Mobile search across primary and secondary (+91, spaces, dashes)
                if (!empty($cleanDigits) && strlen($cleanDigits) >= 5) {
                    $query->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(mobile_num, ''), '-', ''), ' ', ''), '+91', '') LIKE ?", ["%{$cleanDigits}%"])
                          ->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(secondary_mobile_num, ''), '-', ''), ' ', ''), '+91', '') LIKE ?", ["%{$cleanDigits}%"]);

                    if (strlen($cleanDigits) >= 10) {
                        $last10 = substr($cleanDigits, -10);
                        $query->orWhere('mobile_num', 'like', "%{$last10}%")
                              ->orWhere('secondary_mobile_num', 'like', "%{$last10}%");
                    }
                }

                // Normalized Unique ID (mimas_no) / PAN
                if (!empty($cleanAlphanumeric) && strlen($cleanAlphanumeric) >= 3) {
                    $query->orWhereRaw("REPLACE(REPLACE(COALESCE(mimas_no, ''), '-', ''), ' ', '') LIKE ?", ["%{$cleanAlphanumeric}%"])
                          ->orWhereRaw("REPLACE(REPLACE(COALESCE(pan, ''), '-', ''), ' ', '') LIKE ?", ["%{$cleanAlphanumeric}%"]);
                }

                // Related Applications and Project Name matching
                $query->orWhereHas('leaseApplications', function ($lq) use ($q) {
                    $lq->where('application_no', 'like', "%{$q}%")
                        ->orWhere('common_id', 'like', "%{$q}%")
                        ->orWhere('village', 'like', "%{$q}%")
                        ->orWhere('taluk', 'like', "%{$q}%")
                        ->orWhere('other_mineral_name', 'like', "%{$q}%");
                })
                ->orWhereHas('miningApplications', function ($mq) use ($q) {
                    $mq->where('application_no', 'like', "%{$q}%")
                        ->orWhere('common_id', 'like', "%{$q}%")
                        ->orWhere('village', 'like', "%{$q}%")
                        ->orWhere('taluk', 'like', "%{$q}%")
                        ->orWhere('other_mineral_name', 'like', "%{$q}%");
                })
                ->orWhereHas('environmentProjects', function ($eq) use ($q) {
                    $eq->where('project_code', 'like', "%{$q}%")
                        ->orWhere('project_name', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                })
                ->orWhereHas('ecCertificates', function ($cq) use ($q) {
                    $cq->where('ec_ref_no', 'like', "%{$q}%")
                        ->orWhere('parivesh_app_no', 'like', "%{$q}%")
                        ->orWhere('applicant_name', 'like', "%{$q}%");
                })
                ->orWhereHas('pptApplications', function ($pq) use ($q) {
                    $pq->where('application_no', 'like', "%{$q}%")
                        ->orWhere('project_name', 'like', "%{$q}%");
                })
                ->orWhereHas('dgpsSurveys', function ($dq) use ($q) {
                    $dq->where('survey_no', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                })
                ->orWhereHas('droneSurveys', function ($drq) use ($q) {
                    $drq->where('survey_no', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                })
                ->orWhereHas('ecCompliances', function ($ecq) use ($q) {
                    $ecq->where('compliance_no', 'like', "%{$q}%")
                        ->orWhere('project_name', 'like', "%{$q}%");
                });
            })
            ->take(8)
            ->get();

        $results = $customers->map(function ($c) {
            // Determine active stage
            $stage = 'Profile Registered';
            if ($c->ecCompliances()->exists()) {
                $stage = 'EC Compliance Active';
            } elseif ($c->ecCertificates()->exists()) {
                $stage = 'EC Certificate Issued';
            } elseif ($c->pptApplications()->exists()) {
                $stage = 'PPT Department';
            } elseif ($c->environmentProjects()->exists()) {
                $stage = 'Environment Clearance';
            } elseif ($c->miningApplications()->exists()) {
                $stage = 'Mining Plan';
            } elseif ($c->leaseApplications()->exists()) {
                $stage = 'Lease Application';
            } elseif ($c->dgpsSurveys()->exists()) {
                $stage = 'DGPS Survey';
            } elseif ($c->droneSurveys()->exists()) {
                $stage = 'Drone Survey';
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
                'id'               => $c->id,
                'name'             => $c->customer_name,
                'company'          => $c->company_name ?: 'Individual Applicant',
                'unique_id'        => $c->mimas_no ?: ('CUST-' . str_pad($c->id, 4, '0', STR_PAD_LEFT)),
                'mimas_no'         => $c->mimas_no,
                'mobile'           => $c->mobile_num,
                'secondary_mobile' => $c->secondary_mobile_num,
                'aadhaar'          => $maskedAadhaar,
                'district'         => $c->district?->name ?: 'N/A',
                'active_stage'     => $stage,
                'url'              => route('customer-tracking.show', $c->slug ?? $c->id),
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

        // 3. Mobile match: handles 10 digits, +91, leading 0, or formatted numbers across BOTH primary and secondary
        if (!empty($cleanDigits) && strlen($cleanDigits) >= 10) {
            $last10 = substr($cleanDigits, -10);
            $cMobile = Customer::where(function ($mq) use ($q, $cleanDigits, $last10) {
                $mq->where('mobile_num', $q)
                   ->orWhere('mobile_num', $cleanDigits)
                   ->orWhere('mobile_num', $last10)
                   ->orWhere('secondary_mobile_num', $q)
                   ->orWhere('secondary_mobile_num', $cleanDigits)
                   ->orWhere('secondary_mobile_num', $last10)
                   ->orWhereRaw("RIGHT(REPLACE(REPLACE(REPLACE(COALESCE(mobile_num, ''), '-', ''), ' ', ''), '+91', ''), 10) = ?", [$last10])
                   ->orWhereRaw("RIGHT(REPLACE(REPLACE(REPLACE(COALESCE(secondary_mobile_num, ''), '-', ''), ' ', ''), '+91', ''), 10) = ?", [$last10]);
            })->first();

            if ($cMobile) return $cMobile;
        }

        // 4. Customer Unique ID (mimas_no), PAN, or exact raw match
        $c = Customer::where('mimas_no', $q)
            ->orWhere('pan', $q)
            ->first();
        if ($c) return $c;

        if (!empty($cleanAlphanumeric)) {
            $cUnique = Customer::whereRaw("REPLACE(REPLACE(COALESCE(mimas_no, ''), '-', ''), ' ', '') = ?", [$cleanAlphanumeric])
                ->orWhereRaw("REPLACE(REPLACE(COALESCE(pan, ''), '-', ''), ' ', '') = ?", [$cleanAlphanumeric])
                ->first();
            if ($cUnique) return $cUnique;
        }

        // 5. Match via Application No / Common ID / Project Code across all modules
        $lease = LeaseApplication::where('application_no', $q)->orWhere('common_id', $q)->first();
        if ($lease && $lease->customer) return $lease->customer;

        $mining = MiningApplication::where('application_no', $q)->orWhere('common_id', $q)->first();
        if ($mining && $mining->customer) return $mining->customer;

        $env = EnvironmentProject::where('project_code', $q)->first();
        if ($env && $env->customer) return $env->customer;

        $cert = EcCertificate::where('ec_ref_no', $q)->orWhere('parivesh_app_no', $q)->first();
        if ($cert && $cert->customer) return $cert->customer;

        $ppt = PptApplication::where('application_no', $q)->first();
        if ($ppt && $ppt->customer) return $ppt->customer;

        $dgps = DgpsSurvey::where('survey_no', $q)->first();
        if ($dgps && $dgps->customer) return $dgps->customer;

        $drone = DroneSurvey::where('survey_no', $q)->first();
        if ($drone && $drone->customer) return $drone->customer;

        $comp = EcCompliance::where('compliance_no', $q)->first();
        if ($comp && $comp->customer) return $comp->customer;

        // 6. Match via Project Name / Location in applications
        $envProj = EnvironmentProject::where('project_name', 'like', "%{$q}%")->orWhere('location', 'like', "%{$q}%")->first();
        if ($envProj && $envProj->customer) return $envProj->customer;

        $pptApp = PptApplication::where('project_name', 'like', "%{$q}%")->first();
        if ($pptApp && $pptApp->customer) return $pptApp->customer;

        // 7. Fuzzy search fallback by customer_name, company_name, mimas_no, mobile numbers, or aadhaar
        return Customer::where('customer_name', 'like', "%{$q}%")
            ->orWhere('company_name', 'like', "%{$q}%")
            ->orWhere('mimas_no', 'like', "%{$q}%")
            ->orWhere('mobile_num', 'like', "%{$q}%")
            ->orWhere('secondary_mobile_num', 'like', "%{$q}%")
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
            'miningApplications.district',
            'miningApplications.mineral',
            'miningApplications.documents.folder',
            'miningApplications.natureOfWork',
            'miningApplications.planType',
            'environmentProjects.district',
            'environmentProjects.documents.folder',
            'environmentProjects.pptApplications',
            'ecCertificates.environmentProject',
            'pptApplications.district',
            'pptApplications.mineral',
            'pptApplications.documents',
            'dgpsSurveys.documents',
            'droneSurveys.documents',
            'ecCompliances.district',
            'ecCompliances.mineral',
            'ecCompliances.documents',
        ]);

        // Build Multi-Application Chains (supports 10+ applications, multiple quarries, or direct single entries)
        $allChains = [];
        $consumedMiningIds = [];
        $consumedEnvIds = [];
        $consumedPptIds = [];
        $consumedCertIds = [];
        $consumedCompIds = [];
        $consumedDgpsIds = [];
        $consumedDroneIds = [];

        // 1. Process all Leases as Primary Full-Cycle Application Chains (1. Lease -> 8. Drone)
        foreach ($customer->leaseApplications as $l) {
            $mining = $customer->miningApplications->where('lease_application_id', $l->id)->first()
                      ?? ($l->common_id ? $customer->miningApplications->where('common_id', $l->common_id)->first() : null);

            $env = $customer->environmentProjects->where('lease_application_id', $l->id)->first()
                   ?? ($mining ? $customer->environmentProjects->where('mining_application_id', $mining->id)->first() : null);

            $ppt = $env ? $customer->pptApplications->where('environment_project_id', $env->id)->first() : null;
            $cert = $customer->ecCertificates->where('lease_application_id', $l->id)->first()
                    ?? ($env ? $customer->ecCertificates->where('environment_project_id', $env->id)->first() : null);
            $comp = $env ? $customer->ecCompliances->where('environment_project_id', $env->id)->first()
                    : ($cert ? $customer->ecCompliances->where('ec_certificate_id', $cert->id)->first() : null);
            $dgps = $customer->dgpsSurveys->where('lease_application_id', $l->id)->first()
                    ?? ($mining ? $customer->dgpsSurveys->where('mining_application_id', $mining->id)->first() : null);
            $drone = $customer->droneSurveys->where('lease_application_id', $l->id)->first()
                    ?? ($mining ? $customer->droneSurveys->where('mining_application_id', $mining->id)->first() : null);

            if ($mining) $consumedMiningIds[] = $mining->id;
            if ($env) $consumedEnvIds[] = $env->id;
            if ($ppt) $consumedPptIds[] = $ppt->id;
            if ($cert) $consumedCertIds[] = $cert->id;
            if ($comp) $consumedCompIds[] = $comp->id;
            if ($dgps) $consumedDgpsIds[] = $dgps->id;
            if ($drone) $consumedDroneIds[] = $drone->id;

            // Determine highest reached stage for this lease concession
            $currentStageNum = 1;
            $currentStageLabel = 'Stage 1: Lease';
            if ($drone) {
                $currentStageNum = 8;
                $currentStageLabel = 'Stage 8: Drone 3D';
            } elseif ($dgps) {
                $currentStageNum = 7;
                $currentStageLabel = 'Stage 7: DGPS Survey';
            } elseif ($comp) {
                $currentStageNum = 6;
                $currentStageLabel = 'Stage 6: EC Compliance';
            } elseif ($cert) {
                $currentStageNum = 5;
                $currentStageLabel = 'Stage 5: EC Granted';
            } elseif ($ppt) {
                $currentStageNum = 4;
                $currentStageLabel = 'Stage 4: SEAC Appraisal';
            } elseif ($env) {
                $currentStageNum = 3;
                $currentStageLabel = 'Stage 3: EC Clearance';
            } elseif ($mining) {
                $currentStageNum = 2;
                $currentStageLabel = 'Stage 2: Mining Plan';
            }

            $allChains[] = [
                'id'                  => 'lease-' . $l->id,
                'type'                => 'lease_chain',
                'service_code'        => 'lease_chain',
                'service_name'        => 'Full Cycle Concession',
                'title'               => ($l->common_id ? '[' . $l->common_id . '] ' : '') . $l->application_no . ' — ' . ($l->village ?: ($l->district?->name ?? 'Quarry Site')),
                'sub_title'           => ($l->taluk ? $l->taluk . ', ' : '') . ($l->district?->name ?? '') . ($l->area_extent_ha ? ' • ' . number_format($l->area_extent_ha, 2) . ' Ha' : ''),
                'village'             => $l->village ?: 'Quarry Site',
                'taluk'               => $l->taluk ?: '',
                'district_name'       => $l->district?->name ?: 'Tamil Nadu',
                'district_id'         => $l->district_id,
                'area_extent_ha'      => (float) ($l->area_extent_ha ?? 0),
                'mineral_name'        => $l->mineral?->name ?: ($l->other_mineral_name ?: 'Rough Stone / Gravel'),
                'survey_nos'          => $l->surveyNumbers ? $l->surveyNumbers->pluck('survey_no')->filter()->implode(', ') : '',
                'status'              => $l->status ?: 'draft',
                'current_stage_num'   => $currentStageNum,
                'current_stage_label' => $currentStageLabel,
                'progress_pct'        => round(($currentStageNum / 8) * 100),
                'is_direct'           => false,
                'entry_stage'         => 1,
                'action_url'          => route('viewapplication') . '?id=' . $l->id,
                'lease'               => $l,
                'mining'              => $mining,
                'env'                 => $env,
                'ppt'                 => $ppt,
                'cert'                => $cert,
                'compliance'          => $comp,
                'dgps'                => $dgps,
                'drone'               => $drone,
            ];
        }

        // 2. Standalone Mining Plans
        foreach ($customer->miningApplications as $m) {
            if (in_array($m->id, $consumedMiningIds)) continue;
            $env = $customer->environmentProjects->where('mining_application_id', $m->id)->first();
            $ppt = $env ? $customer->pptApplications->where('environment_project_id', $env->id)->first() : null;
            $cert = $env ? $customer->ecCertificates->where('environment_project_id', $env->id)->first() : null;
            $comp = $env ? $customer->ecCompliances->where('environment_project_id', $env->id)->first() : null;
            $dgps = $customer->dgpsSurveys->where('mining_application_id', $m->id)->first();
            $drone = $customer->droneSurveys->where('mining_application_id', $m->id)->first();

            if ($env) $consumedEnvIds[] = $env->id;
            if ($ppt) $consumedPptIds[] = $ppt->id;
            if ($cert) $consumedCertIds[] = $cert->id;
            if ($comp) $consumedCompIds[] = $comp->id;
            if ($dgps) $consumedDgpsIds[] = $dgps->id;
            if ($drone) $consumedDroneIds[] = $drone->id;

            $allChains[] = [
                'id'             => 'mining-' . $m->id,
                'type'           => 'direct_mining',
                'service_code'   => 'mining',
                'service_name'   => 'Direct Mining Plan',
                'icon'           => 'bi-hammer',
                'badge_class'    => 'bg-warning-subtle text-warning-emphasis',
                'ref_no'         => $m->application_no,
                'title'          => ($m->common_id ? '[' . $m->common_id . '] ' : '') . $m->application_no . ' — Direct Mining Plan',
                'sub_title'      => ($m->taluk ? $m->taluk . ', ' : '') . ($m->district?->name ?? 'Quarry Site'),
                'location'       => ($m->village ? $m->village . ', ' : '') . ($m->district?->name ?? 'Quarry Site'),
                'district_name'  => $m->district?->name ?? 'Tamil Nadu',
                'district_id'    => $m->district_id,
                'area_extent_ha' => (float) ($m->area_extent_ha ?? 0),
                'mineral_name'   => $m->mineral?->name ?? 'Rough Stone',
                'status'         => $m->status ?: 'draft',
                'date'           => $m->created_at?->format('d M Y'),
                'action_url'     => url('/process?id=' . $m->id),
                'is_direct'      => true,
                'entry_stage'    => 2,
                'lease'          => null,
                'mining'         => $m,
                'env'            => $env,
                'ppt'            => $ppt,
                'cert'           => $cert,
                'compliance'     => $comp,
                'dgps'           => $dgps,
                'drone'          => $drone,
            ];
        }

        // 3. Standalone Environment Projects
        foreach ($customer->environmentProjects as $e) {
            if (in_array($e->id, $consumedEnvIds)) continue;
            $ppt = $customer->pptApplications->where('environment_project_id', $e->id)->first();
            $cert = $customer->ecCertificates->where('environment_project_id', $e->id)->first();
            $comp = $customer->ecCompliances->where('environment_project_id', $e->id)->first();

            if ($ppt) $consumedPptIds[] = $ppt->id;
            if ($cert) $consumedCertIds[] = $cert->id;
            if ($comp) $consumedCompIds[] = $comp->id;

            $allChains[] = [
                'id'             => 'env-' . $e->id,
                'type'           => 'direct_env',
                'service_code'   => 'environment',
                'service_name'   => 'Environment Clearance (' . ($e->category_badge ?: 'B2') . ')',
                'icon'           => 'bi-tree',
                'badge_class'    => 'bg-success-subtle text-success-emphasis',
                'ref_no'         => $e->project_code,
                'title'          => $e->project_code . ' — Direct Environment Clearance (' . $e->category_badge . ')',
                'sub_title'      => $e->project_name,
                'location'       => $e->location ?: ($e->district?->name ?? 'Quarry Site'),
                'district_name'  => $e->district?->name ?? 'Tamil Nadu',
                'district_id'    => $e->district_id,
                'area_extent_ha' => 0,
                'mineral_name'   => null,
                'status'         => $e->status ?: 'draft',
                'date'           => $e->created_at?->format('d M Y'),
                'action_url'     => route('eviron.show', $e->id),
                'is_direct'      => true,
                'entry_stage'    => 3,
                'lease'          => null,
                'mining'         => null,
                'env'            => $e,
                'ppt'            => $ppt,
                'cert'           => $cert,
                'compliance'     => $comp,
                'dgps'           => null,
                'drone'          => null,
            ];
        }

        // 4. Standalone PPT Applications
        foreach ($customer->pptApplications as $p) {
            if (in_array($p->id, $consumedPptIds)) continue;
            $allChains[] = [
                'id'             => 'ppt-' . $p->id,
                'type'           => 'direct_ppt',
                'service_code'   => 'ppt',
                'service_name'   => 'SEAC Presentation',
                'icon'           => 'bi-easel',
                'badge_class'    => 'bg-primary-subtle text-primary-emphasis',
                'ref_no'         => $p->application_no,
                'title'          => $p->application_no . ' — Direct SEAC Presentation (' . ucwords(str_replace('_', ' ', $p->presentation_stage ?: 'General')) . ')',
                'sub_title'      => $p->project_name,
                'location'       => $p->district?->name ?? 'State Level',
                'district_name'  => $p->district?->name ?? 'Tamil Nadu',
                'district_id'    => $p->district_id,
                'area_extent_ha' => 0,
                'mineral_name'   => $p->mineral?->name,
                'status'         => $p->status ?: 'draft',
                'date'           => $p->created_at?->format('d M Y'),
                'action_url'     => route('ppt-department.show', $p->id),
                'is_direct'      => true,
                'entry_stage'    => 4,
                'lease'          => null,
                'mining'         => null,
                'env'            => null,
                'ppt'            => $p,
                'cert'           => null,
                'compliance'     => null,
                'dgps'           => null,
                'drone'          => null,
            ];
        }

        // 5. Standalone EC Certificates
        foreach ($customer->ecCertificates as $c) {
            if (in_array($c->id, $consumedCertIds)) continue;
            $comp = $customer->ecCompliances->where('ec_certificate_id', $c->id)->first();
            if ($comp) $consumedCompIds[] = $comp->id;

            $allChains[] = [
                'id'             => 'cert-' . $c->id,
                'type'           => 'direct_cert',
                'service_code'   => 'ec_certificate',
                'service_name'   => 'EC Certificate Order',
                'icon'           => 'bi-award',
                'badge_class'    => 'bg-info-subtle text-info-emphasis',
                'ref_no'         => $c->ec_ref_no,
                'title'          => $c->ec_ref_no . ' — Direct EC Certificate',
                'sub_title'      => 'Issued: ' . ($c->issue_date ? $c->issue_date->format('d M Y') : 'Active'),
                'location'       => $c->location ?: 'Quarry Site',
                'district_name'  => 'Tamil Nadu',
                'district_id'    => null,
                'area_extent_ha' => (float) ($c->extent_ha ?? 0),
                'mineral_name'   => null,
                'status'         => $c->status ?: 'active',
                'date'           => $c->issue_date?->format('d M Y') ?: $c->created_at?->format('d M Y'),
                'action_url'     => route('ec-certificate.show', $c->id),
                'is_direct'      => true,
                'entry_stage'    => 5,
                'lease'          => null,
                'mining'         => null,
                'env'            => null,
                'ppt'            => null,
                'cert'           => $c,
                'compliance'     => $comp,
                'dgps'           => null,
                'drone'          => null,
            ];
        }

        // 6. Standalone EC Compliances
        foreach ($customer->ecCompliances as $comp) {
            if (in_array($comp->id, $consumedCompIds)) continue;
            $allChains[] = [
                'id'             => 'compliance-' . $comp->id,
                'type'           => 'direct_compliance',
                'service_code'   => 'ec_compliance',
                'service_name'   => 'Half-Yearly EC Compliance',
                'icon'           => 'bi-clipboard-check',
                'badge_class'    => 'bg-info-subtle text-info-emphasis',
                'ref_no'         => $comp->compliance_no,
                'title'          => $comp->compliance_no . ' — Direct Half-Yearly Compliance (' . ($comp->compliance_period ?: 'Period') . ')',
                'sub_title'      => $comp->project_name ?: 'Statutory Half-Yearly Compliance Filing',
                'location'       => ($comp->village ? $comp->village . ', ' : '') . ($comp->district?->name ?? 'Quarry Site'),
                'district_name'  => $comp->district?->name ?? 'Tamil Nadu',
                'district_id'    => $comp->district_id,
                'area_extent_ha' => 0,
                'mineral_name'   => $comp->mineral?->name,
                'status'         => $comp->status ?: 'draft',
                'date'           => $comp->created_at?->format('d M Y'),
                'action_url'     => route('ec-compliance.show', $comp->id),
                'is_direct'      => true,
                'entry_stage'    => 6,
                'lease'          => null,
                'mining'         => null,
                'env'            => null,
                'ppt'            => null,
                'cert'           => null,
                'compliance'     => $comp,
                'dgps'           => null,
                'drone'          => null,
            ];
        }

        // 7. Standalone DGPS Surveys
        foreach ($customer->dgpsSurveys as $dgps) {
            if (in_array($dgps->id, $consumedDgpsIds)) continue;
            $allChains[] = [
                'id'             => 'dgps-' . $dgps->id,
                'type'           => 'direct_dgps',
                'service_code'   => 'dgps',
                'service_name'   => 'DGPS Land Survey',
                'icon'           => 'bi-geo-alt-fill',
                'badge_class'    => 'bg-indigo-subtle text-indigo',
                'ref_no'         => $dgps->survey_no,
                'title'          => $dgps->survey_no . ' — Direct DGPS Land Survey',
                'sub_title'      => $dgps->location ?: 'Boundary Pillar Survey',
                'location'       => $dgps->location ?: 'Boundary Pillar Demarcation',
                'district_name'  => 'Tamil Nadu',
                'district_id'    => null,
                'area_extent_ha' => (float) ($dgps->lease_area ?? 0),
                'mineral_name'   => null,
                'status'         => $dgps->survey_status ?: 'scheduled',
                'date'           => $dgps->survey_date?->format('d M Y') ?: $dgps->created_at?->format('d M Y'),
                'action_url'     => route('dgps-survey.show', $dgps->id),
                'is_direct'      => true,
                'entry_stage'    => 7,
                'lease'          => null,
                'mining'         => null,
                'env'            => null,
                'ppt'            => null,
                'cert'           => null,
                'compliance'     => null,
                'dgps'           => $dgps,
                'drone'          => null,
            ];
        }

        // 8. Standalone Drone Surveys
        foreach ($customer->droneSurveys as $drone) {
            if (in_array($drone->id, $consumedDroneIds)) continue;
            $allChains[] = [
                'id'             => 'drone-' . $drone->id,
                'type'           => 'direct_drone',
                'service_code'   => 'drone',
                'service_name'   => 'Drone Volumetric Survey',
                'icon'           => 'bi-camera-video-fill',
                'badge_class'    => 'bg-warning-subtle text-warning-emphasis',
                'ref_no'         => $drone->survey_no,
                'title'          => $drone->survey_no . ' — Direct Drone Volumetric Survey',
                'sub_title'      => $drone->location ?: 'Aerial Photogrammetry Survey',
                'location'       => $drone->location ?: 'Quarry Pit Volumetric Scan',
                'district_name'  => 'Tamil Nadu',
                'district_id'    => null,
                'area_extent_ha' => (float) ($drone->lease_area ?? 0),
                'mineral_name'   => null,
                'status'         => $drone->survey_status ?: 'completed',
                'date'           => $drone->flight_date?->format('d M Y') ?: $drone->created_at?->format('d M Y'),
                'action_url'     => route('drone-survey.show', $drone->id),
                'is_direct'      => true,
                'entry_stage'    => 8,
                'lease'          => null,
                'mining'         => null,
                'env'            => null,
                'ppt'            => null,
                'cert'           => null,
                'compliance'     => null,
                'dgps'           => null,
                'drone'          => $drone,
            ];
        }

        // Split into Type 1 (Full Lifecycle Chains) vs Type 2 (Standalone Single Services)
        $fullCycleChains = array_values(array_filter($allChains, fn($c) => !$c['is_direct'] || $c['type'] === 'lease_chain'));
        $standaloneServices = array_values(array_filter($allChains, fn($c) => !empty($c['is_direct'])));

        // Extract District Groups across Full Cycle Chains (e.g. Salem, Dharmapuri, Namakkal)
        $districtGroups = [];
        foreach ($fullCycleChains as $fc) {
            $dName = $fc['district_name'] ?: 'Tamil Nadu';
            $dId = $fc['district_id'] ?: 0;
            if (!isset($districtGroups[$dName])) {
                $districtGroups[$dName] = [
                    'district_id'   => $dId,
                    'district_name' => $dName,
                    'count'         => 0,
                    'total_area'    => 0.0,
                    'chains'        => [],
                ];
            }
            $districtGroups[$dName]['count']++;
            $districtGroups[$dName]['total_area'] += (float) ($fc['area_extent_ha'] ?? 0);
            $districtGroups[$dName]['chains'][] = $fc;
        }
        ksort($districtGroups);

        // Resolve Active Selected Chain based on user switch request
        $requestedChainId = request('chain') ?? (request('lease_id') ? 'lease-' . request('lease_id') : null);
        $requestedTab = request('tab'); // 'full_cycle' or 'standalone'

        $selectedChain = null;
        if ($requestedChainId) {
            foreach ($allChains as $ch) {
                if ($ch['id'] === $requestedChainId) {
                    $selectedChain = $ch;
                    break;
                }
            }
        }

        if (!$selectedChain) {
            if ($requestedTab === 'standalone' && !empty($standaloneServices)) {
                $selectedChain = $standaloneServices[0];
            } elseif (!empty($fullCycleChains)) {
                $selectedChain = $fullCycleChains[0];
            } elseif (!empty($standaloneServices)) {
                $selectedChain = $standaloneServices[0];
            } elseif (!empty($allChains)) {
                $selectedChain = $allChains[0];
            }
        }

        $selectedChainId = $selectedChain['id'] ?? null;

        // Determine Active Tab ('portfolio', 'lifecycle', 'standalone', 'vault')
        if (count($fullCycleChains) === 0) {
            // For clients with 0 full-cycle chains, only standalone or vault can be active
            if (in_array($requestedTab, ['standalone', 'vault'])) {
                $activeTab = $requestedTab;
            } else {
                $activeTab = count($standaloneServices) > 0 ? 'standalone' : 'vault';
            }
        } elseif (in_array($requestedTab, ['portfolio', 'lifecycle', 'standalone', 'vault'])) {
            $activeTab = $requestedTab;
        } elseif ($requestedTab === 'full_cycle') {
            $activeTab = (count($fullCycleChains) > 1 && !$requestedChainId) ? 'portfolio' : 'lifecycle';
        } elseif ($requestedChainId) {
            $activeTab = 'lifecycle';
        } elseif (count($fullCycleChains) > 1) {
            $activeTab = 'portfolio';
        } elseif (count($fullCycleChains) === 1) {
            $activeTab = 'lifecycle';
        } elseif (count($standaloneServices) > 0) {
            $activeTab = 'standalone';
        } else {
            $activeTab = 'portfolio';
        }

        $activeCategory = in_array($activeTab, ['portfolio', 'lifecycle']) ? 'full_cycle' : ($activeTab === 'standalone' ? 'standalone' : 'full_cycle');

        $leaseApp = $selectedChain['lease'] ?? null;
        $miningApp = $selectedChain['mining'] ?? null;
        $envProj = $selectedChain['env'] ?? null;
        $pptApp = $selectedChain['ppt'] ?? null;
        $ecCert = $selectedChain['cert'] ?? null;
        $ecCompliance = $selectedChain['compliance'] ?? null;
        $dgpsSurvey = $selectedChain['dgps'] ?? null;
        $droneSurvey = $selectedChain['drone'] ?? null;
        $isDirectEntry = $selectedChain['is_direct'] ?? false;
        $entryStage = $selectedChain['entry_stage'] ?? 1;

        // Canonical 8-Stage Lifecycle Stepper
        $stepper = [
            1 => [
                'name'        => 'Lease Application',
                'short_name'  => 'Lease',
                'status'      => $leaseApp ? ($leaseApp->status === 'approved' ? 'completed' : 'in_progress') : ($isDirectEntry && $entryStage > 1 ? 'bypassed' : 'pending'),
                'app_no'      => $leaseApp?->application_no ?: ($isDirectEntry && $entryStage > 1 ? 'Direct Walk-in' : 'Not started'),
                'date'        => $leaseApp?->created_at ? $leaseApp->created_at->format('d M Y') : null,
                'badge_color' => $leaseApp ? ($leaseApp->status === 'approved' ? 'success' : 'warning') : ($isDirectEntry && $entryStage > 1 ? 'info' : 'secondary'),
                'icon'        => 'bi-file-earmark-text',
                'url'         => $leaseApp ? (route('viewapplication') . '?id=' . $leaseApp->id) : route('step1'),
            ],
            2 => [
                'name'        => 'Mining Plan',
                'short_name'  => 'Mining',
                'status'      => $miningApp ? ($miningApp->status === 'approved' ? 'completed' : 'in_progress') : ($isDirectEntry && $entryStage > 2 ? 'bypassed' : 'pending'),
                'app_no'      => $miningApp?->application_no ?: ($isDirectEntry && $entryStage > 2 ? 'Direct Walk-in' : 'Not started'),
                'date'        => $miningApp?->created_at ? $miningApp->created_at->format('d M Y') : null,
                'badge_color' => $miningApp ? ($miningApp->status === 'approved' ? 'success' : 'warning') : ($isDirectEntry && $entryStage > 2 ? 'info' : 'secondary'),
                'icon'        => 'bi-hammer',
                'url'         => $miningApp ? url('/process?id=' . $miningApp->id) : route('newapplication'),
            ],
            3 => [
                'name'        => 'Environment Clearance',
                'short_name'  => 'EC (B1/B2)',
                'status'      => $envProj ? ($envProj->status === 'approved' ? 'completed' : 'in_progress') : ($isDirectEntry && $entryStage > 3 ? 'bypassed' : 'pending'),
                'app_no'      => $envProj?->project_code ?: ($isDirectEntry && $entryStage > 3 ? 'Direct Walk-in' : 'Not started'),
                'date'        => $envProj?->created_at ? $envProj->created_at->format('d M Y') : null,
                'badge_color' => $envProj ? ($envProj->status === 'approved' ? 'success' : 'warning') : ($isDirectEntry && $entryStage > 3 ? 'info' : 'secondary'),
                'icon'        => 'bi-tree',
                'url'         => $envProj ? route('eviron.show', $envProj->id) : route('eviron.index'),
                'sub_category'=> $envProj?->category_badge,
            ],
            4 => [
                'name'        => 'PPT Presentation',
                'short_name'  => 'PPT / SEAC',
                'status'      => $pptApp ? (in_array(strtolower($pptApp->status), ['approved', 'completed']) ? 'completed' : 'in_progress') : ($envProj ? 'ready' : ($isDirectEntry && $entryStage > 4 ? 'bypassed' : 'pending')),
                'app_no'      => $pptApp?->application_no ?: ($envProj ? 'Awaiting ToR' : ($isDirectEntry && $entryStage > 4 ? 'Direct Walk-in' : 'Pending EC')),
                'date'        => $pptApp?->created_at ? $pptApp->created_at->format('d M Y') : null,
                'badge_color' => $pptApp ? (in_array(strtolower($pptApp->status), ['approved', 'completed']) ? 'success' : 'info') : ($envProj ? 'warning' : 'secondary'),
                'icon'        => 'bi-easel',
                'url'         => $pptApp ? route('ppt-department.show', $pptApp->id) : route('ppt-department.step', 1),
                'is_loop'     => ($envProj && $envProj->category === 'B1'),
                'loop_details'=> ($envProj && $envProj->category === 'B1') ? ($envProj->b1_stage === 'completed' ? 'ToR & Final EIA Approved' : 'ToR ↔ EIA 2-Round Loop') : null,
            ],
            5 => [
                'name'        => 'EC Certificate',
                'short_name'  => 'EC Order',
                'status'      => $ecCert ? 'completed' : ($envProj && $envProj->status === 'approved' ? 'ready' : ($isDirectEntry && $entryStage > 5 ? 'bypassed' : 'pending')),
                'app_no'      => $ecCert?->ec_ref_no ?: 'Not issued',
                'date'        => $ecCert?->issue_date ? $ecCert->issue_date->format('d M Y') : null,
                'badge_color' => $ecCert ? 'success' : ($envProj && $envProj->status === 'approved' ? 'warning' : 'secondary'),
                'icon'        => 'bi-award',
                'url'         => $ecCert ? route('ec-certificate.show', $ecCert->id) : route('ec-certificate.step', 1),
            ],
            6 => [
                'name'        => 'EC Compliance',
                'short_name'  => 'Half-Yearly',
                'status'      => $ecCompliance ? (in_array(strtolower($ecCompliance->status), ['completed', 'uploaded_to_parivesh']) ? 'completed' : 'in_progress') : ($ecCert ? 'ready' : 'pending'),
                'app_no'      => $ecCompliance?->compliance_no ?: 'Not filed',
                'date'        => $ecCompliance?->created_at ? $ecCompliance->created_at->format('d M Y') : null,
                'badge_color' => $ecCompliance ? (in_array(strtolower($ecCompliance->status), ['completed', 'uploaded_to_parivesh']) ? 'success' : 'info') : ($ecCert ? 'warning' : 'secondary'),
                'icon'        => 'bi-clipboard-check',
                'url'         => $ecCompliance ? route('ec-compliance.show', $ecCompliance->id) : route('ec-compliance.step', 1),
            ],
            7 => [
                'name'        => 'DGPS Land Survey',
                'short_name'  => 'DGPS Survey',
                'status'      => $dgpsSurvey ? (in_array(strtolower($dgpsSurvey->survey_status), ['completed', 'verified']) ? 'completed' : 'in_progress') : 'pending',
                'app_no'      => $dgpsSurvey?->survey_no ?: 'Not scheduled',
                'date'        => $dgpsSurvey?->survey_date ? $dgpsSurvey->survey_date->format('d M Y') : null,
                'badge_color' => $dgpsSurvey ? (in_array(strtolower($dgpsSurvey->survey_status), ['completed', 'verified']) ? 'success' : 'info') : 'secondary',
                'icon'        => 'bi-geo-alt-fill',
                'url'         => $dgpsSurvey ? route('dgps-survey.show', $dgpsSurvey->id) : route('dgps-survey.step', 1),
            ],
            8 => [
                'name'        => 'Drone Survey',
                'short_name'  => 'Drone 3D',
                'status'      => $droneSurvey ? (in_array(strtolower($droneSurvey->survey_status), ['completed', 'verified', 'deliverables_ready', 'report_signed']) ? 'completed' : 'in_progress') : 'pending',
                'app_no'      => $droneSurvey?->survey_no ?: 'Not scheduled',
                'date'        => $droneSurvey?->flight_date ? $droneSurvey->flight_date->format('d M Y') : null,
                'badge_color' => $droneSurvey ? (in_array(strtolower($droneSurvey->survey_status), ['completed', 'verified', 'deliverables_ready', 'report_signed']) ? 'success' : 'info') : 'secondary',
                'icon'        => 'bi-camera-video-fill',
                'url'         => $droneSurvey ? route('drone-survey.show', $droneSurvey->id) : route('drone-survey.step', 1),
            ],
        ];

        // Calculate Overall Progress (0% to 100%)
        $completedSteps = collect($stepper)->filter(fn($s) => $s['status'] === 'completed')->count();
        $inProgressSteps = collect($stepper)->filter(fn($s) => in_array($s['status'], ['in_progress', 'ready']))->count();
        $progressPercent = min(100, round(($completedSteps * 12.5) + ($inProgressSteps * 6.25)));

        // Consolidated Document Vault across all 8 modules
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

        // 4. PPT Documents
        if ($pptApp) {
            foreach ($pptApp->documents as $doc) {
                if ($doc->file_path) {
                    $allDocuments->push([
                        'module'      => 'PPT Department',
                        'module_code' => 'ppt',
                        'folder'      => 'Presentation Files',
                        'name'        => $doc->document_name ?: 'SEAC Presentation File',
                        'file_name'   => $doc->file_name ?: basename($doc->file_path),
                        'file_path'   => $doc->file_path,
                        'file_size'   => $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : '—',
                        'status'      => $doc->status ?: 'uploaded',
                        'date'        => $doc->created_at ? $doc->created_at->format('d M Y') : 'Saved',
                    ]);
                }
            }
        }

        // 5. EC Certificate File
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

        // 6. EC Compliance Documents
        if ($ecCompliance) {
            foreach ($ecCompliance->documents as $doc) {
                if ($doc->file_path) {
                    $allDocuments->push([
                        'module'      => 'EC Compliance',
                        'module_code' => 'compliance',
                        'folder'      => ucwords(str_replace('_', ' ', $doc->folder_category ?: 'Compliance Docs')),
                        'name'        => $doc->document_name,
                        'file_name'   => $doc->file_name,
                        'file_path'   => $doc->file_path,
                        'file_size'   => $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : '—',
                        'status'      => $doc->status ?: 'uploaded',
                        'date'        => $doc->created_at ? $doc->created_at->format('d M Y') : 'Saved',
                    ]);
                }
            }
        }

        // 7. DGPS Documents
        if ($dgpsSurvey) {
            foreach ($dgpsSurvey->documents as $doc) {
                if ($doc->file_path) {
                    $allDocuments->push([
                        'module'      => 'DGPS Land Survey',
                        'module_code' => 'dgps',
                        'folder'      => 'Survey Deliverables',
                        'name'        => $doc->document_name ?: 'DGPS Boundary Pillar Document',
                        'file_name'   => $doc->file_name ?: basename($doc->file_path),
                        'file_path'   => $doc->file_path,
                        'file_size'   => $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : '—',
                        'status'      => $doc->status ?: 'uploaded',
                        'date'        => $doc->created_at ? $doc->created_at->format('d M Y') : 'Saved',
                    ]);
                }
            }
        }

        // 8. Drone Documents
        if ($droneSurvey) {
            foreach ($droneSurvey->documents as $doc) {
                if ($doc->file_path) {
                    $allDocuments->push([
                        'module'      => 'Drone Survey',
                        'module_code' => 'drone',
                        'folder'      => 'Volumetric Deliverables',
                        'name'        => $doc->document_name ?: 'Drone Photogrammetry File',
                        'file_name'   => $doc->file_name ?: basename($doc->file_path),
                        'file_path'   => $doc->file_path,
                        'file_size'   => $doc->file_size ? number_format($doc->file_size / 1024, 1) . ' KB' : '—',
                        'status'      => $doc->status ?: 'uploaded',
                        'date'        => $doc->created_at ? $doc->created_at->format('d M Y') : 'Saved',
                    ]);
                }
            }
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
            'stepper'            => $stepper,
            'progressPercent'    => $progressPercent,
            'leaseApp'           => $leaseApp,
            'miningApp'          => $miningApp,
            'envProj'            => $envProj,
            'pptApp'             => $pptApp,
            'ecCert'             => $ecCert,
            'ecCompliance'       => $ecCompliance,
            'dgpsSurvey'         => $dgpsSurvey,
            'droneSurvey'        => $droneSurvey,
            'ecValidity'         => $ecValidity,
            'allDocuments'       => $allDocuments,
            'applicationChains'  => $allChains,
            'fullCycleChains'    => $fullCycleChains,
            'standaloneServices' => $standaloneServices,
            'districtGroups'     => $districtGroups,
            'activeCategory'     => $activeCategory,
            'activeTab'          => $activeTab,
            'selectedChainId'    => $selectedChainId,
            'selectedChain'      => $selectedChain,
            'isDirectEntry'      => $isDirectEntry,
            'entryStage'         => $entryStage,
        ];
    }

    /**
     * Render the official consolidated Proforma Invoice
     */
    public function proformaInvoice($customer, Request $request = null)
    {
        $request = $request ?: request();
        $customerModel = $this->resolveCustomer($customer);
        $invoiceData = $this->buildInvoiceData($customerModel, 'proforma', $request);
        return view('pages.customer_tracking.proforma_invoice', $invoiceData);
    }

    /**
     * Render the official Tax Invoice
     */
    public function taxInvoice($customer, Request $request = null)
    {
        $request = $request ?: request();
        $customerModel = $this->resolveCustomer($customer);
        $invoiceData = $this->buildInvoiceData($customerModel, 'tax', $request);
        return view('pages.customer_tracking.tax_invoice', $invoiceData);
    }

    /**
     * Resolve Customer model from slug, ID, or Aadhaar
     */
    protected function resolveCustomer($customer): Customer
    {
        if ($customer instanceof Customer) {
            return $customer;
        }

        $cleanDigits = preg_replace('/[^0-9]/', '', (string)$customer);
        return Customer::where('slug', $customer)
            ->orWhere('id', $customer)
            ->orWhere('mimas_no', $customer)
            ->when(strlen($cleanDigits) >= 8, function ($q) use ($cleanDigits, $customer) {
                $q->orWhere('aadhaar_no', $customer)
                  ->orWhereRaw("REPLACE(REPLACE(COALESCE(aadhaar_no, ''), '-', ''), ' ', '') = ?", [$cleanDigits]);
            })
            ->firstOrFail();
    }

    /**
     * Build an exhaustive catalog of all customer applications across all 8 modules
     */
    public function getCustomerApplicationsCatalog(Customer $customer): \Illuminate\Support\Collection
    {
        $customer->loadMissing([
            'district',
            'mineral',
            'leaseApplications.district',
            'leaseApplications.mineral',
            'miningApplications.district',
            'miningApplications.mineral',
            'environmentProjects.district',
            'ecCertificates',
            'ecCompliances.district',
            'ecCompliances.mineral',
            'pptApplications.district',
            'pptApplications.mineral',
            'dgpsSurveys',
            'droneSurveys',
        ]);

        $catalog = collect();

        // 1. Mining Plan Applications
        foreach ($customer->miningApplications as $m) {
            $dist = $m->district?->name ?: ($customer->district?->name ?: 'Tamil Nadu');
            $mineral = $m->mineral?->name ?: ($customer->mineral?->name ?: 'Rough Stone and Gravel');
            $loc = trim(($m->survey_numbers_text ? 'S.F.Nos. ' . $m->survey_numbers_text . ', ' : '') .
                        ($m->village ? $m->village . ' Village, ' : '') .
                        ($m->taluk ? $m->taluk . ' Taluk, ' : '') .
                        "{$dist} Dist.");
            $val = (float)$m->product_value;
            $amt = $val > 0 ? $val : 120000.00;

            $catalog->push([
                'key'            => 'mining_' . $m->id,
                'id'             => $m->id,
                'module'         => 'mining',
                'module_label'   => 'Mining Plan',
                'badge_class'    => 'bg-primary text-white',
                'badge_color'    => '#2563eb',
                'app_no'         => $m->application_no ?: ('MP-' . str_pad($m->id, 4, '0', STR_PAD_LEFT)),
                'common_id'      => $m->common_id,
                'mineral'        => $mineral,
                'district'       => $dist,
                'location'       => $loc,
                'extent'         => $m->area_extent_ha ? ($m->area_extent_ha . ' Ha') : null,
                'status'         => $m->status ?: 'Active',
                'amount'         => $amt,
                'has_real_val'   => $val > 0,
                'paid_amount'    => (float)$m->paid_amount,
                'sac'            => '998343',
                'tax_title'      => "Preparation of Mining Plan and Progressive Mine Closure Plan (PMCP) under Rule 41 of Tamil Nadu Minor Mineral Concession Rules, 1959",
                'proforma_title' => "Preparation of Mining Plan",
                'bullets'        => [
                    "DGPS Survey and demarcation of boundary pillars with precise georeferencing",
                    "Mine Plan drafting, geological reserve estimation & year-wise production scheduling",
                    "Progressive Mine Closure Plan (PMCP) & environmental safeguard designs",
                    "Statutory submission to the Department of Geology and Mining for approval",
                ],
            ]);
        }

        // 2. Mining Lease Applications
        foreach ($customer->leaseApplications as $l) {
            $dist = $l->district?->name ?: ($customer->district?->name ?: 'Tamil Nadu');
            $mineral = $l->mineral?->name ?: ($customer->mineral?->name ?: 'Rough Stone and Gravel');
            $loc = trim(($l->village ? $l->village . ' Village, ' : '') .
                        ($l->taluk ? $l->taluk . ' Taluk, ' : '') .
                        "{$dist} Dist.");
            $val = (float)$l->product_value;
            $amt = $val > 0 ? $val : 150000.00;

            $catalog->push([
                'key'            => 'lease_' . $l->id,
                'id'             => $l->id,
                'module'         => 'lease',
                'module_label'   => 'Lease Application',
                'badge_class'    => 'bg-success text-white',
                'badge_color'    => '#059669',
                'app_no'         => $l->application_no ?: ('LA-' . str_pad($l->id, 4, '0', STR_PAD_LEFT)),
                'common_id'      => $l->common_id,
                'mineral'        => $mineral,
                'district'       => $dist,
                'location'       => $loc,
                'extent'         => $l->area_extent_ha ? ($l->area_extent_ha . ' Ha') : null,
                'status'         => $l->status ?: 'Active',
                'amount'         => $amt,
                'has_real_val'   => $val > 0,
                'paid_amount'    => (float)$l->paid_amount,
                'sac'            => '998341',
                'tax_title'      => "Preparation of Mining Lease Application, Revenue Scrutiny & Statutory MMS Filing",
                'proforma_title' => "Mining Lease Application & Revenue Documentation",
                'bullets'        => [
                    "Collation and scrutiny of Patta, Chitta, Adangal, and FMB sketches",
                    "Combined village survey sketch verification & district revenue clearance",
                    "Affidavit preparation and statutory online portal (MMS) filing",
                    "Follow-up with District Collectorate and Department of Geology & Mining",
                ],
            ]);
        }

        // 3. Environmental Clearance Projects
        foreach ($customer->environmentProjects as $e) {
            $dist = $e->district?->name ?: ($customer->district?->name ?: 'Tamil Nadu');
            $mineral = $customer->mineral?->name ?: 'Rough Stone and Gravel';
            $loc = $e->location ?: ($dist . ' Dist.');
            $val = (float)$e->product_value;
            $amt = $val > 0 ? $val : 80000.00;

            $catalog->push([
                'key'            => 'env_' . $e->id,
                'id'             => $e->id,
                'module'         => 'environment',
                'module_label'   => 'Environment Clearance (EC)',
                'badge_class'    => 'bg-info text-white',
                'badge_color'    => '#0284c7',
                'app_no'         => $e->project_code ?: ('EC-' . str_pad($e->id, 4, '0', STR_PAD_LEFT)),
                'common_id'      => null,
                'mineral'        => $mineral,
                'district'       => $dist,
                'location'       => $loc,
                'extent'         => null,
                'status'         => $e->status ?: 'In Progress',
                'amount'         => $amt,
                'has_real_val'   => $val > 0,
                'paid_amount'    => (float)$e->paid_amount,
                'sac'            => '998349',
                'tax_title'      => "Environmental Clearance (EC) Project Formulation, Form-1, Form-2 & Baseline EMP",
                'proforma_title' => "Preparation of Form-1, Form-2, PFR & EMP for Environmental Clearance",
                'bullets'        => [
                    "Preparation of statutory Form-1, Form-2, and Pre-Feasibility Report (PFR)",
                    "Baseline Environmental Management Plan (EMP) & mitigation safeguards",
                    "Online portal submission on PARIVESH (MoEFCC / SEIAA-TN)",
                    "Preparation of technical dossiers for SEAC Committee appraisal",
                ],
            ]);
        }

        // 4. EC Certificates
        foreach ($customer->ecCertificates as $ec) {
            $dist = $customer->district?->name ?: 'Tamil Nadu';
            $mineral = $customer->mineral?->name ?: 'Rough Stone and Gravel';
            $loc = $customer->district?->name ? ($customer->district->name . ' Dist.') : 'Tamil Nadu';
            $val = (float)$ec->product_value;
            $amt = $val > 0 ? $val : 50000.00;

            $catalog->push([
                'key'            => 'cert_' . $ec->id,
                'id'             => $ec->id,
                'module'         => 'ec_certificate',
                'module_label'   => 'EC Certificate',
                'badge_class'    => 'bg-warning text-dark',
                'badge_color'    => '#d97706',
                'app_no'         => $ec->ec_ref_no ?: ('CERT-' . str_pad($ec->id, 4, '0', STR_PAD_LEFT)),
                'common_id'      => null,
                'mineral'        => $mineral,
                'district'       => $dist,
                'location'       => $loc,
                'extent'         => null,
                'status'         => $ec->status ?: 'Issued',
                'amount'         => $amt,
                'has_real_val'   => $val > 0,
                'paid_amount'    => (float)$ec->paid_amount,
                'sac'            => '998349',
                'tax_title'      => "SEIAA Environmental Clearance Certificate Order Verification, Compliance Docket & Archive",
                'proforma_title' => "SEIAA EC Certificate Order Verification & Statutory Filing",
                'bullets'        => [
                    "SEIAA-TN Environmental Clearance order issuance scrutiny and terms verification",
                    "Preparation of baseline compliance docket and statutory register maintenance",
                    "Notification compliance to District Collectorate and DEAC authorities",
                ],
            ]);
        }

        // 5. PPT Presentations
        foreach ($customer->pptApplications as $p) {
            $dist = $p->district?->name ?: ($customer->district?->name ?: 'Tamil Nadu');
            $mineral = $p->mineral?->name ?: ($customer->mineral?->name ?: 'Rough Stone and Gravel');
            $loc = $dist . ' Dist.';
            $val = (float)$p->product_value;
            $amt = $val > 0 ? $val : 30000.00;

            $catalog->push([
                'key'            => 'ppt_' . $p->id,
                'id'             => $p->id,
                'module'         => 'ppt',
                'module_label'   => 'PPT Presentation',
                'badge_class'    => 'bg-secondary text-white',
                'badge_color'    => '#475569',
                'app_no'         => $p->application_no ?: ('PPT-' . str_pad($p->id, 4, '0', STR_PAD_LEFT)),
                'common_id'      => null,
                'mineral'        => $mineral,
                'district'       => $dist,
                'location'       => $loc,
                'extent'         => null,
                'status'         => $p->status ?: 'Completed',
                'amount'         => $amt,
                'has_real_val'   => $val > 0,
                'paid_amount'    => (float)$p->paid_amount,
                'sac'            => '998311',
                'tax_title'      => "PPT Statutory Presentation Formulation & SEAC/SEIAA Technical Appraisal Defense",
                'proforma_title' => "PPT Department Presentation & SEIAA Appraisal Defense",
                'bullets'        => [
                    "Comprehensive technical appraisal slide deck formulation for SEAC committee",
                    "CER undertaking documentation and environmental mitigation slides",
                    "SEAC query reply preparation and technical committee defense assistance",
                ],
            ]);
        }

        // 6. DGPS Land Surveys
        foreach ($customer->dgpsSurveys as $d) {
            $dist = $customer->district?->name ?: 'Tamil Nadu';
            $mineral = $customer->mineral?->name ?: 'Rough Stone and Gravel';
            $loc = $d->location ?: ($dist . ' Dist.');
            $val = (float)$d->product_value;
            $amt = $val > 0 ? $val : 35000.00;

            $catalog->push([
                'key'            => 'dgps_' . $d->id,
                'id'             => $d->id,
                'module'         => 'dgps',
                'module_label'   => 'DGPS Land Survey',
                'badge_class'    => 'bg-dark text-white',
                'badge_color'    => '#0f172a',
                'app_no'         => $d->survey_no ?: ('DGPS-' . str_pad($d->id, 4, '0', STR_PAD_LEFT)),
                'common_id'      => null,
                'mineral'        => $mineral,
                'district'       => $dist,
                'location'       => $loc,
                'extent'         => null,
                'status'         => $d->status ?: 'Completed',
                'amount'         => $amt,
                'has_real_val'   => $val > 0,
                'paid_amount'    => (float)$d->paid_amount,
                'sac'            => '998341',
                'tax_title'      => "DGPS Boundary Survey, Baseline Control Fixation & Pillar Coordinates Demarcation Plan",
                'proforma_title' => "DGPS Land Survey & Boundary Demarcation",
                'bullets'        => [
                    "Differential GPS baseline observation & high-precision pillar coordinate table",
                    "Geo-referenced KML file preparation and overlay with Survey of India Topo-sheet",
                    "Comprehensive boundary demarcation map with certified survey sketch",
                ],
            ]);
        }

        // 7. Drone Volumetric Surveys
        foreach ($customer->droneSurveys as $dr) {
            $dist = $customer->district?->name ?: 'Tamil Nadu';
            $mineral = $customer->mineral?->name ?: 'Rough Stone and Gravel';
            $loc = $dr->location ?: ($dist . ' Dist.');
            $val = (float)$dr->product_value;
            $amt = $val > 0 ? $val : 50000.00;

            $catalog->push([
                'key'            => 'drone_' . $dr->id,
                'id'             => $dr->id,
                'module'         => 'drone',
                'module_label'   => 'Drone Volumetric Survey',
                'badge_class'    => 'bg-primary text-white',
                'badge_color'    => '#1d4ed8',
                'app_no'         => $dr->survey_no ?: ('DRONE-' . str_pad($dr->id, 4, '0', STR_PAD_LEFT)),
                'common_id'      => null,
                'mineral'        => $mineral,
                'district'       => $dist,
                'location'       => $loc,
                'extent'         => null,
                'status'         => $dr->status ?: 'Completed',
                'amount'         => $amt,
                'has_real_val'   => $val > 0,
                'paid_amount'    => (float)$dr->paid_amount,
                'sac'            => '998342',
                'tax_title'      => "Drone Aerial Photogrammetry Survey, High-Resolution Orthomosaic & 3D Volumetric Computation Report",
                'proforma_title' => "Drone Aerial Volumetric Survey & 3D Modeling",
                'bullets'        => [
                    "DGCA-compliant UAV aerial photogrammetry survey of quarry concession area",
                    "High-resolution orthomosaic map, Digital Elevation Model (DEM / DTM) generation",
                    "Precise cut and fill volumetric excavation analysis report for statutory compliance",
                ],
            ]);
        }

        // 8. EC Compliance Monitoring
        foreach ($customer->ecCompliances as $comp) {
            $dist = $comp->district?->name ?: ($customer->district?->name ?: 'Tamil Nadu');
            $mineral = $comp->mineral?->name ?: ($customer->mineral?->name ?: 'Rough Stone and Gravel');
            $loc = $comp->project_name ?: ($dist . ' Dist.');
            $val = (float)$comp->product_value;
            $amt = $val > 0 ? $val : 40000.00;

            $catalog->push([
                'key'            => 'comp_' . $comp->id,
                'id'             => $comp->id,
                'module'         => 'ec_compliance',
                'module_label'   => 'EC Half-Yearly Compliance',
                'badge_class'    => 'bg-teal text-white',
                'badge_color'    => '#0d9488',
                'app_no'         => $comp->compliance_no ?: ('COMP-' . str_pad($comp->id, 4, '0', STR_PAD_LEFT)),
                'common_id'      => null,
                'mineral'        => $mineral,
                'district'       => $dist,
                'location'       => $loc,
                'extent'         => null,
                'status'         => $comp->status ?: 'Active',
                'amount'         => $amt,
                'has_real_val'   => $val > 0,
                'paid_amount'    => (float)$comp->paid_amount,
                'sac'            => '998349',
                'tax_title'      => "Environmental Clearance Half-Yearly Compliance Monitoring, NABL Lab Environmental Testing & MoEFCC Parivesh Portal Filing",
                'proforma_title' => "EC Half-Yearly Compliance Monitoring & Testing",
                'bullets'        => [
                    "Conducting baseline AAQ, Water quality, Noise, and Vibration tests via NABL laboratory",
                    "Compilation of six-monthly environmental clearance condition monitoring report",
                    "Online submission on PARIVESH portal and physical submission to IRO MoEFCC / SEIAA-TN",
                ],
            ]);
        }

        return $catalog;
    }

    /**
     * Assemble dynamic invoice data aggregated across all applications with multi-select support
     */
    protected function buildInvoiceData(Customer $customer, string $type, ?Request $request = null): array
    {
        $catalog = $this->getCustomerApplicationsCatalog($customer);

        // Resolve selected application keys from request (comma-separated or array)
        $selectedKeys = [];
        if ($request) {
            if ($request->has('apps')) {
                $raw = $request->input('apps');
                $selectedKeys = is_array($raw) ? $raw : explode(',', (string)$raw);
            } elseif ($request->has('selected_apps')) {
                $raw = $request->input('selected_apps');
                $selectedKeys = is_array($raw) ? $raw : explode(',', (string)$raw);
            }
        }
        $selectedKeys = array_values(array_filter(array_map('trim', $selectedKeys)));

        // If no filter specified (initial visit):
        // Default to all items that have real recorded value > 0;
        // If none have recorded value > 0, default to all items so nothing is blank!
        if (empty($selectedKeys)) {
            $realValKeys = $catalog->where('has_real_val', true)->pluck('key')->all();
            $selectedKeys = !empty($realValKeys) ? $realValKeys : $catalog->pluck('key')->all();
        }

        // Filter catalog by selected keys
        $selectedCatalog = $catalog->filter(function ($item) use ($selectedKeys) {
            return in_array($item['key'], $selectedKeys);
        });

        // Ensure we have at least 1 item if catalog is not empty
        if ($selectedCatalog->isEmpty()) {
            if ($catalog->isNotEmpty()) {
                $selectedCatalog = collect([$catalog->first()]);
                $selectedKeys = [$catalog->first()['key']];
            } else {
                // Standalone fallback if customer has no applications registered yet
                $defaultDist = $customer->district?->name ?: 'Tamil Nadu';
                $defaultMineral = $customer->mineral?->name ?: 'Rough Stone and Gravel';
                $defaultItem = [
                    'key'            => 'default_mining',
                    'id'             => 0,
                    'module'         => 'mining',
                    'module_label'   => 'Mining Plan',
                    'badge_class'    => 'bg-primary text-white',
                    'badge_color'    => '#2563eb',
                    'app_no'         => 'MP-STD',
                    'common_id'      => null,
                    'mineral'        => $defaultMineral,
                    'district'       => $defaultDist,
                    'location'       => "in respect of {$defaultMineral} Quarry, {$defaultDist} District, Tamil Nadu",
                    'extent'         => null,
                    'status'         => 'Active',
                    'amount'         => 150000.00,
                    'has_real_val'   => false,
                    'paid_amount'    => 0,
                    'sac'            => '998343',
                    'tax_title'      => "Preparation of Mining Plan and Progressive Mine Closure Plan (PMCP) under Rule 41 of Tamil Nadu Minor Mineral Concession Rules, 1959",
                    'proforma_title' => "Preparation of Mining Plan",
                    'bullets'        => [
                        "DGPS Survey and demarcation of boundary pillars with precise georeferencing",
                        "Mine Plan drafting, geological reserve estimation & year-wise production scheduling",
                        "Progressive Mine Closure Plan (PMCP) & environmental safeguard designs",
                        "Statutory submission to the Department of Geology and Mining for approval",
                    ],
                ];
                $catalog = collect([$defaultItem]);
                $selectedCatalog = $catalog;
                $selectedKeys = ['default_mining'];
            }
        }

        // Contextual profile header info
        $firstApp = $selectedCatalog->first();
        $mineralName = $firstApp['mineral'] ?? ($customer->mineral?->name ?: 'Rough Stone and Gravel');
        $districtName = $firstApp['district'] ?? ($customer->district?->name ?: 'Tamil Nadu');
        $locationDetails = !empty($firstApp['location']) ? $firstApp['location'] : "{$districtName} District, Tamil Nadu";

        // Assemble line items
        $items = [];
        $sl = 1;

        foreach ($selectedCatalog as $app) {
            $refText = "in respect of {$app['mineral']} quarry" . (!empty($app['location']) ? " {$app['location']}" : '') . (!empty($app['extent']) ? " (Extent: {$app['extent']})" : '');

            if ($type === 'tax') {
                $fullDesc = "{$app['tax_title']} {$refText}";
                $items[] = [
                    'sl'           => $sl++,
                    'key'          => $app['key'],
                    'app_no'       => $app['app_no'],
                    'module'       => $app['module'],
                    'module_label' => $app['module_label'],
                    'service_name' => $app['tax_title'],
                    'name'         => $fullDesc,
                    'ref'          => $refText,
                    'location'     => $app['location'],
                    'sac'          => $app['sac'],
                    'amount'       => (float)$app['amount'],
                    'bullets'      => $app['bullets'],
                ];
            } else {
                $items[] = [
                    'sl'           => $sl++,
                    'key'          => $app['key'],
                    'app_no'       => $app['app_no'],
                    'module'       => $app['module'],
                    'module_label' => $app['module_label'],
                    'service_name' => $app['proforma_title'],
                    'title'        => $app['proforma_title'],
                    'ref'          => $refText,
                    'location'     => $app['location'],
                    'sac'          => $app['sac'],
                    'amount'       => (float)$app['amount'],
                    'bullets'      => $app['bullets'],
                ];
            }
        }

        $subtotal = collect($items)->sum('amount');
        $cgst = round($subtotal * 0.09, 2);
        $sgst = round($subtotal * 0.09, 2);
        $grandTotal = $subtotal + $cgst + $sgst;
        $amountInWords = $this->numberToWords($grandTotal);

        $isConsolidated = count($items) > 1;
        $particulars = $isConsolidated
            ? "Consolidated Statutory Mining Consultancy & Technical Services (" . count($items) . " Services Package)"
            : ($items[0]['service_name'] ?? 'Statutory Mining Consultancy Services');

        $miningApp = $customer->miningApplications->first();
        $leaseApp = $customer->leaseApplications->first();
        $envProj = $customer->environmentProjects->first();
        $ecCert = $customer->ecCertificates->first();
        $pptApp = $customer->pptApplications->first();
        $dgps = $customer->dgpsSurveys->first();
        $drone = $customer->droneSurveys->first();

        return [
            'customer'         => $customer,
            'miningApp'        => $miningApp,
            'leaseApp'         => $leaseApp,
            'envProj'          => $envProj,
            'ecCert'           => $ecCert,
            'pptApp'           => $pptApp,
            'dgps'             => $dgps,
            'drone'            => $drone,
            'mineralName'      => $mineralName,
            'districtName'     => $districtName,
            'locationDetails'  => $locationDetails,
            'particulars'      => $particulars,
            'contactPerson'    => $customer->customer_name ?: 'Authorized Representative',
            'contactNo'        => $customer->mobile_num ?: '+91 94439 37841',
            'workOrderVia'     => 'Direct Engagement / Formal Work Order',
            'items'            => $items,
            'subtotal'         => $subtotal,
            'cgst'             => $cgst,
            'sgst'             => $sgst,
            'grandTotal'       => $grandTotal,
            'amountInWords'    => $amountInWords,
            'invoiceType'      => $type,
            'isConsolidated'   => $isConsolidated,
            'invoiceNo'        => $type === 'tax'
                ? 'GTMS/TI/' . date('Y') . '/' . str_pad($customer->id, 4, '0', STR_PAD_LEFT)
                : 'GTMS/PI/' . date('Y') . '/' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
            'tiInvoiceNo'      => 'GTMS/TI/' . date('Y') . '/' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
            'piInvoiceNo'      => 'GTMS/PI/' . date('Y') . '/' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
            'invoiceDate'      => date('d-M-Y'),
            'workOrderNo'      => 'WO-GTMS-' . ($firstApp['common_id'] ?? ($miningApp?->common_id ?: date('Y') . '-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT))),
            'workOrderDate'    => date('d-M-Y', strtotime('-5 days')),
            'allCustomerApps'  => $catalog,
            'selectedAppKeys'  => $selectedKeys,
            'sealImageUrl'     => \App\Http\Controllers\Accounts\InvoiceSealController::getCustomSealUrl(),
        ];
    }

    /**
     * Convert currency number into formal Indian wording (Rupees & Paise)
     */
    protected function numberToWords(float $number): string
    {
        $no = (int)floor($number);
        $decimal = (int)round(($number - $no) * 100);
        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
            30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
            80 => 'Eighty', 90 => 'Ninety'
        ];

        if ($no === 0) {
            return 'Zero Rupees Only';
        }

        $crores = (int)floor($no / 10000000);
        $no %= 10000000;
        $lakhs = (int)floor($no / 100000);
        $no %= 100000;
        $thousands = (int)floor($no / 1000);
        $no %= 1000;
        $hundreds = (int)floor($no / 100);
        $remainder = $no % 100;

        $parts = [];

        if ($crores > 0) {
            $parts[] = $this->convertTwoDigits($crores, $words) . ' Crore';
        }
        if ($lakhs > 0) {
            $parts[] = $this->convertTwoDigits($lakhs, $words) . ' Lakh';
        }
        if ($thousands > 0) {
            $parts[] = $this->convertTwoDigits($thousands, $words) . ' Thousand';
        }
        if ($hundreds > 0) {
            $parts[] = $words[$hundreds] . ' Hundred';
        }
        if ($remainder > 0) {
            $parts[] = $this->convertTwoDigits($remainder, $words);
        }

        $res = implode(' ', array_filter($parts)) . ' Rupees';
        if ($decimal > 0) {
            $res .= ' and ' . $this->convertTwoDigits($decimal, $words) . ' Paise';
        }
        return trim($res) . ' Only';
    }

    private function convertTwoDigits(int $n, array $words): string
    {
        if ($n < 20) {
            return $words[$n];
        }
        $tens = (int)(floor($n / 10) * 10);
        $units = $n % 10;
        return trim(($words[$tens] ?? '') . ' ' . ($words[$units] ?? ''));
    }
}

