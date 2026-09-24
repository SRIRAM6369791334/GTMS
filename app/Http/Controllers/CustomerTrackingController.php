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
        if ($customer) {
            $dossierData = $this->buildCustomerDossier($customer);
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
            'miningApplications.documents.folder',
            'miningApplications.natureOfWork',
            'miningApplications.planType',
            'environmentProjects.documents.folder',
            'ecCertificates.environmentProject',
            'pptApplications',
            'dgpsSurveys',
            'droneSurveys',
            'ecCompliances',
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

    /**
     * Render the official consolidated Proforma Invoice
     */
    public function proformaInvoice($customer)
    {
        $customerModel = $this->resolveCustomer($customer);
        $invoiceData = $this->buildInvoiceData($customerModel, 'proforma');
        return view('pages.customer_tracking.proforma_invoice', $invoiceData);
    }

    /**
     * Render the official Tax Invoice
     */
    public function taxInvoice($customer)
    {
        $customerModel = $this->resolveCustomer($customer);
        $invoiceData = $this->buildInvoiceData($customerModel, 'tax');
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
     * Assemble dynamic invoice data aggregated across all 7 applications
     */
    protected function buildInvoiceData(Customer $customer, string $type): array
    {
        $customer->loadMissing([
            'district',
            'mineral',
            'leaseApplications',
            'miningApplications.district',
            'miningApplications.mineral',
            'environmentProjects',
            'ecCertificates',
            'ecCompliances',
            'pptApplications',
            'dgpsSurveys',
            'droneSurveys',
        ]);

        $miningApp = $customer->miningApplications->first();
        $leaseApp = $customer->leaseApplications->first();
        $envProj = $customer->environmentProjects->first();
        $ecCert = $customer->ecCertificates->first();
        $ecCompliance = $customer->ecCompliances->first();
        $pptApp = $customer->pptApplications->first();
        $dgps = $customer->dgpsSurveys->first();
        $drone = $customer->droneSurveys->first();

        // Concession Profile context
        $mineralName = $customer->mineral?->name
            ?: ($miningApp?->mineral?->name
            ?: ($leaseApp?->mineral?->name ?: 'Rough Stone and Gravel'));

        $districtName = $customer->district?->name
            ?: ($miningApp?->district?->name
            ?: ($leaseApp?->district?->name ?: 'Salem'));

        $locationDetails = $miningApp
            ? "over an extent of " . ($miningApp->area_extent_ha ?: '3.85.0') . " Hectares in S.F.Nos. " . ($miningApp->survey_numbers_text ?: '102/1A, 102/1B') . ", " . ($miningApp->village ?: 'Alathur') . " Village, " . ($miningApp->taluk ?: 'Sankari') . " Taluk, {$districtName} District, Tamil Nadu."
            : ($leaseApp
                ? "in respect of {$mineralName} Quarry over patta lands in {$districtName} District, Tamil Nadu."
                : "in respect of proposed {$mineralName} Quarry Project, {$districtName} District, Tamil Nadu.");

        $items = [];

        if ($type === 'tax') {
            // Tax Invoice: Detailed statutory deliverables
            $recordedServices = [];

            if ($miningApp && (float)$miningApp->product_value > 0) {
                $recordedServices[] = [
                    'name'   => "Preparation of Mining Plan and Progressive Mine Closure Plan (PMCP) under Rule 41 of Tamil Nadu Minor Mineral Concession Rules, 1959 in respect of {$mineralName} Quarry {$locationDetails}",
                    'sac'    => '998343',
                    'amount' => (float)$miningApp->product_value,
                ];
            }
            if ($leaseApp && (float)$leaseApp->product_value > 0) {
                $recordedServices[] = [
                    'name'   => "Preparation of Mining Lease Application, Revenue Scrutiny & Statutory MMS Filing for {$mineralName} Quarry {$locationDetails}",
                    'sac'    => '998341',
                    'amount' => (float)$leaseApp->product_value,
                ];
            }
            if ($envProj && (float)$envProj->product_value > 0) {
                $recordedServices[] = [
                    'name'   => "Environmental Clearance (EC) Project Formulation, Form-1, Form-2 & Baseline EMP for {$mineralName} Quarry {$locationDetails}",
                    'sac'    => '998349',
                    'amount' => (float)$envProj->product_value,
                ];
            }
            if ($ecCert && (float)$ecCert->product_value > 0) {
                $recordedServices[] = [
                    'name'   => "SEIAA Environmental Clearance Certificate Order Verification, Compliance Docket & Archive for {$mineralName} Quarry",
                    'sac'    => '998349',
                    'amount' => (float)$ecCert->product_value,
                ];
            }
            if ($pptApp && (float)$pptApp->product_value > 0) {
                $recordedServices[] = [
                    'name'   => "PPT Statutory Presentation Formulation & SEAC/SEIAA Technical Appraisal Defense for {$mineralName} Quarry",
                    'sac'    => '998311',
                    'amount' => (float)$pptApp->product_value,
                ];
            }
            if ($dgps && (float)$dgps->product_value > 0) {
                $recordedServices[] = [
                    'name'   => "DGPS Boundary Survey, Baseline Control Fixation & Pillar Coordinates Demarcation Plan for {$mineralName} Quarry",
                    'sac'    => '998341',
                    'amount' => (float)$dgps->product_value,
                ];
            }
            if ($drone && (float)$drone->product_value > 0) {
                $recordedServices[] = [
                    'name'   => "Drone Aerial Photogrammetry Survey, High-Resolution Orthomosaic & 3D Volumetric Computation Report for {$mineralName} Quarry",
                    'sac'    => '998342',
                    'amount' => (float)$drone->product_value,
                ];
            }
            if ($ecCompliance && (float)$ecCompliance->product_value > 0) {
                $recordedServices[] = [
                    'name'   => "Environmental Clearance Half-Yearly Compliance Monitoring, NABL Lab Environmental Testing & MoEFCC Parivesh Portal Filing for {$mineralName} Quarry",
                    'sac'    => '998349',
                    'amount' => (float)$ecCompliance->product_value,
                ];
            }

            // Standard fallback if none recorded yet
            if (empty($recordedServices)) {
                $recordedServices[] = [
                    'name'   => "Preparation of Mining Plan and Progressive Mine Closure Plan (PMCP) under Rule 41 of Tamil Nadu Minor Mineral Concession Rules, 1959 in respect of {$mineralName} Quarry {$locationDetails}",
                    'sac'    => '998343',
                    'amount' => 150000.00,
                ];
            }

            $items = $recordedServices;
            $subtotal = collect($items)->sum('amount');
        } else {
            // Proforma Invoice: 4-Pillar Package matching Reference PI standard
            $mpVal = ($miningApp && (float)$miningApp->product_value > 0) ? (float)$miningApp->product_value : 120000.00;
            $ecVal = ($envProj && (float)$envProj->product_value > 0) ? (float)$envProj->product_value : 80000.00;
            $dgpsVal = ($dgps && (float)$dgps->product_value > 0) ? (float)$dgps->product_value : 35000.00;
            $droneVal = ($drone && (float)$drone->product_value > 0) ? (float)$drone->product_value : 50000.00;

            $items = [
                [
                    'title'   => 'Preparation of Mining Plan',
                    'sac'     => '998343',
                    'bullets' => [
                        'DGPS Survey and demarcation of boundary pillars',
                        'Mine Plan drafting, geological reserve estimation & year-wise production scheduling',
                        'Progressive Mine Closure Plan (PMCP) & environmental safeguard designs',
                        'Statutory submission to the Department of Geology and Mining for approval',
                    ],
                    'amount'  => $mpVal,
                ],
                [
                    'title'   => 'Preparation of Form-1, Form-2, PFR & EMP for Environmental Clearance',
                    'sac'     => '998349',
                    'bullets' => [
                        'Preparation of statutory Form-1, Form-2, and Pre-Feasibility Report (PFR)',
                        'Baseline Environmental Management Plan (EMP) & mitigation safeguards',
                        'Online portal submission on PARIVESH (MoEFCC / SEIAA-TN)',
                        'Preparation of PowerPoint Presentation (PPT) for SEAC Appraisal Committee meeting',
                    ],
                    'amount'  => $ecVal,
                ],
                [
                    'title'   => 'DGPS Land Survey & Boundary Demarcation',
                    'sac'     => '998341',
                    'bullets' => [
                        'Differential GPS baseline observation & high-precision pillar coordinate table',
                        'Geo-referenced KML file preparation and overlay with Survey of India Topo-sheet',
                        'Comprehensive boundary demarcation map with certified survey sketch',
                    ],
                    'amount'  => $dgpsVal,
                ],
                [
                    'title'   => 'Drone Aerial Volumetric Survey & 3D Modeling',
                    'sac'     => '998342',
                    'bullets' => [
                        'DGCA-compliant UAV aerial photogrammetry survey of quarry concession area',
                        'High-resolution orthomosaic map, Digital Elevation Model (DEM / DTM) generation',
                        'Precise cut and fill volumetric excavation analysis report for statutory compliance',
                    ],
                    'amount'  => $droneVal,
                ],
            ];

            if ($leaseApp && (float)$leaseApp->product_value > 0) {
                $items[] = [
                    'title'   => 'Mining Lease Application & Revenue Documentation',
                    'sac'     => '998341',
                    'bullets' => [
                        'Collation and scrutiny of Patta, Chitta, Adangal, and FMB sketches',
                        'Affidavit verification, combined village sketch & online MMS statutory filing',
                    ],
                    'amount'  => (float)$leaseApp->product_value,
                ];
            }

            if ($pptApp && (float)$pptApp->product_value > 0) {
                $items[] = [
                    'title'   => 'PPT Department Presentation & SEIAA Appraisal Defense',
                    'sac'     => '998311',
                    'bullets' => [
                        'Comprehensive appraisal slide deck formulation & SEAC queries liaison',
                        'CER undertaking documents, SPCB demand note assistance & hearing assistance',
                    ],
                    'amount'  => (float)$pptApp->product_value,
                ];
            }

            $subtotal = collect($items)->sum('amount');
        }

        $cgst = round($subtotal * 0.09, 2);
        $sgst = round($subtotal * 0.09, 2);
        $grandTotal = $subtotal + $cgst + $sgst;
        $amountInWords = $this->numberToWords($grandTotal);

        return [
            'customer'        => $customer,
            'miningApp'       => $miningApp,
            'leaseApp'        => $leaseApp,
            'envProj'         => $envProj,
            'ecCert'          => $ecCert,
            'pptApp'          => $pptApp,
            'dgps'            => $dgps,
            'drone'           => $drone,
            'mineralName'     => $mineralName,
            'districtName'    => $districtName,
            'locationDetails' => $locationDetails,
            'items'           => $items,
            'subtotal'        => $subtotal,
            'cgst'            => $cgst,
            'sgst'            => $sgst,
            'grandTotal'      => $grandTotal,
            'amountInWords'   => $amountInWords,
            'invoiceType'     => $type,
            'invoiceNo'       => $type === 'tax'
                ? 'GTMS/TI/' . date('Y') . '/' . str_pad($customer->id, 4, '0', STR_PAD_LEFT)
                : 'GTMS/PI/' . date('Y') . '/' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
            'invoiceDate'     => date('d-M-Y'),
            'workOrderNo'     => 'WO-GTMS-' . ($miningApp?->common_id ?: date('Y') . '-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT)),
            'workOrderDate'   => date('d-M-Y', strtotime('-5 days')),
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

