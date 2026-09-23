<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\EnvironmentProject;
use App\Models\EcCertificate;
use App\Models\EcCompliance;
use App\Models\EcComplianceDocument;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;

class EcComplianceSeeder extends Seeder
{
    public function run(): void
    {
        if (EcCompliance::count() > 0) {
            return;
        }

        $customers = Customer::all();
        if ($customers->isEmpty()) {
            return;
        }

        $districts = District::all();
        $minerals = Mineral::all();
        $envProjects = EnvironmentProject::all();
        $ecCerts = EcCertificate::all();

        $samples = [
            [
                'no'        => 'HYC-2026-0001',
                'cust_idx'  => 0,
                'project'   => 'Semmandapatti Black Granite Quarry Half-Yearly Compliance',
                'village'   => 'Semmandapatti Village, Omalur Taluk',
                'period'    => 'April 2026 - September 2026',
                'due_date'  => '2026-12-01',
                'sub_date'  => '2026-11-20',
                'ack_no'    => 'PARIVESH-ACK-TN-2026-8812',
                'lab_name'  => 'Glens Innovation Labs (NABL #TC-7712)',
                'lab_cert'  => 'NABL/ENV/2026/0441',
                'mon_date'  => '2026-08-15',
                'status'    => 'uploaded_to_parivesh',
                'val'       => 50000.00,
                'paid'      => 50000.00,
                'p_status'  => 'paid',
            ],
            [
                'no'        => 'HYC-2026-0002',
                'cust_idx'  => min(1, $customers->count() - 1),
                'project'   => 'Perundurai Rough Stone Compliance & Environmental Audit',
                'village'   => 'Perundurai Village, Erode',
                'period'    => 'October 2025 - March 2026',
                'due_date'  => '2026-06-01',
                'sub_date'  => '2026-05-24',
                'ack_no'    => 'PARIVESH-ACK-TN-2026-6140',
                'lab_name'  => 'Tamil Nadu Test House (NABL #TC-5910)',
                'lab_cert'  => 'NABL/ENV/2026/0119',
                'mon_date'  => '2026-02-10',
                'status'    => 'completed',
                'val'       => 45000.00,
                'paid'      => 45000.00,
                'p_status'  => 'paid',
            ],
            [
                'no'        => 'HYC-2026-0003',
                'cust_idx'  => min(2, $customers->count() - 1),
                'project'   => 'Melur Multi-Colour Granite HYCR Submission',
                'village'   => 'Melur Taluk, Madurai',
                'period'    => 'April 2026 - September 2026',
                'due_date'  => '2026-12-01',
                'sub_date'  => null,
                'ack_no'    => null,
                'lab_name'  => 'Alpha NABL Testing Lab',
                'lab_cert'  => 'NABL/ENV/2026/0890',
                'mon_date'  => '2026-09-02',
                'status'    => 'lab_analysed',
                'val'       => 48000.00,
                'paid'      => 24000.00,
                'p_status'  => 'partial',
            ],
            [
                'no'        => 'HYC-2026-0004',
                'cust_idx'  => min(3, $customers->count() - 1),
                'project'   => 'Kinathukadavu Blue Metal Quarry Environmental Audit',
                'village'   => 'Kinathukadavu, Coimbatore',
                'period'    => 'April 2026 - September 2026',
                'due_date'  => '2026-12-01',
                'sub_date'  => null,
                'ack_no'    => null,
                'lab_name'  => null,
                'lab_cert'  => null,
                'mon_date'  => null,
                'status'    => 'documents_collected',
                'val'       => 40000.00,
                'paid'      => 0.00,
                'p_status'  => 'pending',
            ],
        ];

        foreach ($samples as $s) {
            $cust = $customers[$s['cust_idx']] ?? $customers->first();
            $dist = $districts->random();
            $min = $minerals->isNotEmpty() ? $minerals->random()->id : 1;
            $envProj = $envProjects->isNotEmpty() ? $envProjects->random()->id : null;
            $ecCert = $ecCerts->isNotEmpty() ? $ecCerts->random()->id : null;

            $compliance = EcCompliance::create([
                'compliance_no'               => $s['no'],
                'customer_id'                 => $cust->id,
                'environment_project_id'      => $envProj,
                'ec_certificate_id'           => $ecCert,
                'project_name'                => $s['project'],
                'district_id'                 => $dist->id,
                'taluk_village'               => $s['village'],
                'mineral_id'                  => $min,
                'compliance_period'           => $s['period'],
                'compliance_year'             => '2026',
                'submission_due_date'         => $s['due_date'],
                'submission_date'             => $s['sub_date'],
                'parivesh_app_no'             => 'SIA/TN/MIN/' . rand(10000, 99999) . '/2026',
                'parivesh_acknowledgement_no' => $s['ack_no'],
                'parivesh_uploaded_date'      => $s['sub_date'],
                'nabl_lab_name'               => $s['lab_name'],
                'nabl_certificate_no'         => $s['lab_cert'],
                'monitoring_date'             => $s['mon_date'],
                'status'                      => $s['status'],
                'product_value'               => $s['val'],
                'paid_amount'                 => $s['paid'],
                'pending_amount'              => max(0, $s['val'] - $s['paid']),
                'payment_status'              => $s['p_status'],
                'payment_notes'               => 'Compliance audit and environmental monitoring package',
            ]);

            // Handlers
            ApplicationHandler::create([
                'application_type' => 'ec_compliance',
                'application_id'   => $compliance->id,
                'name'             => 'Dr. N. Sundararajan',
                'role'             => 'Senior Environmental Auditor & EMP Specialist',
                'notes'            => 'Overall half-yearly compliance coordination and Parivesh submission',
                'sort_order'       => 1,
            ]);
            ApplicationHandler::create([
                'application_type' => 'ec_compliance',
                'application_id'   => $compliance->id,
                'name'             => 'R. Selvakumar',
                'role'             => 'NABL Environmental Sampling Officer',
                'notes'            => 'Ambient air, noise decibels, and water quality field collection',
                'sort_order'       => 2,
            ]);

            // Payment
            ApplicationPayment::create([
                'application_type' => 'ec_compliance',
                'application_id'   => $compliance->id,
                'product_value'    => $s['val'],
                'paid_amount'      => $s['paid'],
                'pending_amount'   => max(0, $s['val'] - $s['paid']),
                'payment_status'   => $s['p_status'],
                'notes'            => 'Environmental compliance audit and NABL testing charges',
            ]);

            // Seed key documents from the 4 categories
            $sampleDocs = [
                ['cat' => 'documents', 'name' => '1. 500m Radius Letter'],
                ['cat' => 'documents', 'name' => '3. CTO (Consent to Operate)'],
                ['cat' => 'documents', 'name' => '4. Lease Deed'],
                ['cat' => 'documents', 'name' => '9. Greenbelt & Fencing Photo'],
                ['cat' => 'documents', 'name' => '19. CCTV Camera Installation from Project'],
                ['cat' => 'site_analysis', 'name' => '1. Ambient Air Quality Monitoring Report (PM10, PM2.5, SO2, NOx)'],
                ['cat' => 'site_analysis', 'name' => '2. Noise Level Monitoring Report (Day & Night)'],
                ['cat' => 'report', 'name' => '3. EC- Compliance Report (Point-by-Point Status)'],
                ['cat' => 'parivesh_upload', 'name' => '1. Parivesh MoEFCC Online Submission Receipt & Letter'],
            ];

            foreach ($sampleDocs as $d) {
                EcComplianceDocument::create([
                    'ec_compliance_id' => $compliance->id,
                    'folder_category'  => $d['cat'],
                    'document_name'    => $d['name'],
                    'file_name'        => 'sample_' . strtolower(str_replace(' ', '_', substr($d['name'], 3, 15))) . '.pdf',
                    'file_path'        => 'uploads/compliance/' . $compliance->compliance_no . '/sample.pdf',
                    'file_type'        => 'pdf',
                    'file_size'        => rand(250000, 950000),
                    'is_mandatory'     => true,
                    'status'           => 'uploaded',
                ]);
            }
        }
    }
}
