<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $salem = District::where('name', 'Salem')->first();
        $cbe = District::where('name', 'Coimbatore')->first();
        $madurai = District::where('name', 'Madurai')->first();
        $erode = District::where('name', 'Erode')->first();
        $trichy = District::where('name', 'Tiruchirappalli')->first();

        $gravel = Mineral::where('name', 'like', '%Gravel%')->first() ?? Mineral::first();
        $roughStone = Mineral::where('name', 'like', '%Rough Stone%')->first() ?? Mineral::first();
        $granite = Mineral::where('name', 'like', '%Granite%')->first() ?? Mineral::first();

        $customers = [
            [
                'customer_name' => 'R. Kumaresan',
                'company_name' => 'Sri Bala Traders',
                'mimas_no' => 'TN-MMS-SLM-001',
                'mobile_num' => '9842155670',
                'email' => 'sribala@example.com',
                'district_id' => $salem?->id,
                'mineral_id' => $gravel?->id,
                'pan' => 'AAACS1234F',
                'aadhaar_no' => '9876-5432-1001',
                'gstin' => '33AAACS1234F1Z5',
                'area' => 14.20,
                'address' => 'No. 45/2, Omalur Main Road, Salem, Tamil Nadu - 636004',
                'status' => 1,
            ],
            [
                'customer_name' => 'K. Selvamani',
                'company_name' => 'Maruthi Blue Metals',
                'mimas_no' => 'TN-MMS-CBE-002',
                'mobile_num' => '9443288910',
                'email' => 'selvam@maruthi.com',
                'district_id' => $cbe?->id,
                'mineral_id' => $roughStone?->id,
                'pan' => 'BBAAK2345M',
                'aadhaar_no' => '9876-5432-1002',
                'gstin' => '33BBAAK2345M1Z2',
                'area' => 8.50,
                'address' => 'Sf No 128, Pollachi Main Road, Kinathukadavu, Coimbatore - 642109',
                'status' => 1,
            ],
            [
                'customer_name' => 'P. Venkatesh',
                'company_name' => 'Venkateshwara Granite',
                'mimas_no' => 'TN-MMS-MDU-003',
                'mobile_num' => '9789012345',
                'email' => 'venkat@vgranites.in',
                'district_id' => $madurai?->id,
                'mineral_id' => $granite?->id,
                'pan' => 'CCEPV3456P',
                'aadhaar_no' => '9876-5432-1003',
                'gstin' => '33CCEPV3456P1Z8',
                'area' => 12.00,
                'address' => 'Sf No 44/1A, Melur Road, Madurai - 625106',
                'status' => 0,
            ],
            [
                'customer_name' => 'S. Thangavelu',
                'company_name' => 'Thangam Minerals',
                'mimas_no' => 'TN-MMS-ERD-004',
                'mobile_num' => '9843377112',
                'email' => 'contact@thangam.com',
                'district_id' => $erode?->id,
                'mineral_id' => $roughStone?->id,
                'pan' => 'DDFST4567T',
                'aadhaar_no' => '9876-5432-1004',
                'gstin' => '33DDFST4567T1Z4',
                'area' => 6.75,
                'address' => 'SF 89, SIPCOT Industrial Growth Estate, Perundurai, Erode - 638052',
                'status' => 1,
            ],
            [
                'customer_name' => 'M. Rajendran',
                'company_name' => 'Cauvery Riverbed Minerals',
                'mimas_no' => 'TN-MMS-TRI-005',
                'mobile_num' => '9944022331',
                'email' => 'rajendran@cauvery.in',
                'district_id' => $trichy?->id,
                'mineral_id' => $gravel?->id,
                'pan' => 'EEMRA5678R',
                'aadhaar_no' => '9876-5432-1005',
                'gstin' => '33EEMRA5678R1Z1',
                'area' => 22.40,
                'address' => 'Cauvery River Reach, Musiri, Tiruchirappalli - 621211',
                'status' => 0,
            ],
        ];

        foreach ($customers as $c) {
            Customer::updateOrCreate(
                ['pan' => $c['pan']],
                $c
            );
        }
    }
}
