<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MiningNatureOfWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $works = [
            'Mining Plan',
            'Stockyard',
            'Mine Closure Plan',
            'Scope Work',
            'Opening Notice',
            'E-Tender',
            'Short Term',
            'Others'
        ];

        $workIds = [];
        foreach ($works as $work) {
            $existing = DB::table('nature_of_works')->where('name', $work)->first();
            if ($existing) {
                $id = $existing->id;
            } else {
                $id = DB::table('nature_of_works')->insertGetId([
                    'name' => $work,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $workIds[$work] = $id;
        }

        // Clean previous document fields for nature of works
        DB::table('document_fields')->whereNotNull('nature_of_work_id')->delete();

        $miningModuleId = DB::table('modules')->where('code', 'mining')->value('id');
        if (!$miningModuleId) {
            return;
        }

        $folders = [
            'Field Log' => DB::table('folders')->where('name', 'Field Log')->where('module_id', $miningModuleId)->value('id'),
            'Documents' => DB::table('folders')->where('name', 'Documents')->where('module_id', $miningModuleId)->value('id'),
            'Site Photos' => DB::table('folders')->where('name', 'Site Photos')->where('module_id', $miningModuleId)->value('id'),
            'Report' => DB::table('folders')->where('name', 'Report')->where('module_id', $miningModuleId)->value('id'),
            'Plan' => DB::table('folders')->where('name', 'Plan')->where('module_id', $miningModuleId)->value('id'),
            'Others' => DB::table('folders')->where('name', 'Others')->where('module_id', $miningModuleId)->value('id'),
        ];

        $checklists = [
            'Mining Plan' => [
                'Field Log' => ['Observation Sheet', 'Data Sheet', 'Preparation Sheet', 'Way Points'],
                'Documents' => ['Application', 'Tender Gazette', 'FMB', 'Precise Area Communication Letter', 'Previous Approval Letter', 'Previous Approved Mining Plan', 'Environmental Clearance', 'Proceeding Letter', 'Lease Deed Agreement', 'TNPCB Certificate', 'Permit Letter', 'Existing Pit Letter', 'NOC Letter', 'SEAC Minutes', 'SEIAA Minutes', 'Patta', 'A-Register', 'Adangal', 'Sale Deed', 'Consent Letter', 'GST Certificate', 'Partnership Deed', 'Memorandum of Association', 'Company PAN Card', 'Authorization Letter', 'ID Proof', 'Others'],
                'Site Photos' => ['Quarry Site Photographs'],
                'Report' => ['Covering Letter', 'Text Report', 'Photocopy Documents'],
                'Plan' => ['KML', 'Plan Drawing', 'Mining Plan Drawing', 'Reserve Calculation Table', 'Source Files'],
                'Others' => ['DGPS Data', 'Drone Survey Data'],
            ],
            'Stockyard' => [
                'Field Log' => ['Data Sheet', 'Preparation Sheet', 'Way Points'],
                'Documents' => ['FMB', 'A-Register', 'Adangal', 'Patta Copy', 'Sale Deed', 'Consent Documents', 'TNPCB Documents', 'Aadhar Card', 'Pan Card', 'Company Registration Document', 'GST Certificate', 'Partnership deed Document', 'Memorandum of Association', 'Form - D'],
                'Site Photos' => ['Quarry Site Photographs'],
                'Report' => ['Form A', 'Front Page', 'Letter Head certificate', 'Photocopy', 'Text', 'Form-D'],
                'Plan' => [],
                'Others' => [],
            ],
            'Mine Closure Plan' => [
                'Field Log' => ['Data Sheet', 'Preparation Sheet', 'Way Points'],
                'Documents' => ['Tender Gazette', 'FMB', 'Precise Area Communication Letter', 'Previous Approval Letter', 'Previous Approved Mining Plan', 'Environmental Clearance', 'Proceeding Letter', 'Lease Deed Agreement', 'TNPCB Certificate', 'NOC Letter', 'Patta', 'A-Register', 'Adangal', 'Sale Deed', 'Consent Letter', 'GST Certificate', 'Partnership Deed', 'Memorandum of Association', 'Company PAN Card', 'Authorized Signatory Letter', 'ID Proof'],
                'Site Photos' => ['Quarry Site Photographs'],
                'Report' => ['Text'],
                'Plan' => ['KML', 'Plan Drawing', 'Reserve Calculation Table', 'Source Files'],
                'Others' => ['DGPS Data', 'Drone Survey Data'],
            ],
            'Scope Work' => [
                'Field Log' => ['Data Sheet', 'Preparation Sheet', 'Way Points'],
                'Documents' => ['FMB', 'Patta', 'A-Register', 'Adangal', 'Sale Deed', 'Consent Letter', 'GST Certificate', 'Partnership Deed', 'Memorandum of Association', 'Company PAN Card', 'Authorized Signatory Letter', 'ID Proof', 'Others'],
                'Site Photos' => ['Quarry Site Photographs'],
                'Report' => [],
                'Plan' => ['KML', 'Plan Drawing', 'Reserve Calculation Table', 'Source Files'],
                'Others' => [],
            ],
            'Opening Notice' => [
                'Field Log' => ['Data Sheet', 'Preparation Sheet', 'Way Points'],
                'Documents' => ['FMB', 'Precise Area Communication Letter', 'Previous Approval Letter', 'Previous Approved Mining Plan', 'Environmental Clearance', 'Proceeding Letter', 'Lease Deed Agreement', 'TNPCB Certificate', 'Permit Letter', 'Existing Pit Letter', 'NOC Letter', 'Patta', 'A-Register', 'Adangal', 'Sale Deed', 'Consent Letter', 'GST Certificate', 'Partnership Deed', 'Memorandum of Association', 'Company PAN Card', 'Authorized Signatory Letter', 'ID Proof', 'Others'],
                'Site Photos' => ['Quarry Site Photographs'],
                'Report' => [],
                'Plan' => ['KML', 'Plan Drawing', 'Reserve Calculation Table', 'Source Files'],
                'Others' => [],
            ],
            'E-Tender' => [
                'Field Log' => ['Data Sheet', 'Preparation Sheet', 'Way Points'],
                'Documents' => ['Tender Gazette (Tamil)', 'Tender Gazette (English)', 'Tender Notice', 'Annexure I', 'Annexure II', 'Annexure III', 'Lease Application', 'BOQ', 'FMB', 'A-Register', 'Sworn Affidavit for No Mining Due', 'Sworn Affidavit for No Mining Lease', 'Sworn Affidavit for No Income Tax', 'Mining Due Clearance Certificate', 'Self-Declaration', 'Tender Fee Payment', 'EMD Exemption', 'GST Certificate', 'Partnership Deed', 'Memorandum of Association', 'Company PAN Card', 'MSME', 'Authorized Signatory Letter', 'IT Returns', 'ID Proof', 'Others'],
                'Site Photos' => ['Quarry Site Photographs'],
                'Report' => [],
                'Plan' => ['KML', 'Plan Drawing', 'Reserve Calculation Table', 'Source Files'],
                'Others' => ['Annexure-II Tender Lease Application', 'Annexure - I', 'Annexure III - Declaration Form', 'Self Declaration', 'Others'],
            ],
            'Short Term' => [
                'Field Log' => ['Data Sheet', 'Preparation Sheet', 'Way Points'],
                'Documents' => ['Application', 'FMB', 'A-Register', 'Adangal', 'Patta Copy', 'Sale Deed', 'Consent Documents', 'Aadhar Card', 'Pan Card'],
                'Site Photos' => ['Quarry Site Photographs'],
                'Report' => ['Text'],
                'Plan' => ['KML', 'Plan Drawing', 'Reserve Calculation Table', 'Source Files'],
                'Others' => [],
            ],
            'Others' => [
                'Field Log' => ['Data Sheet', 'Way Points'],
                'Documents' => ['Application', 'FMB', 'A-Register', 'Adangal', 'Patta Copy', 'Sale Deed', 'Consent Documents', 'Aadhar Card', 'Pan Card'],
                'Site Photos' => ['Quarry Site Photographs'],
                'Report' => [],
                'Plan' => ['KML', 'Plan Drawing', 'Reserve Calculation Table', 'Source Files'],
                'Others' => [],
            ],
        ];

        foreach ($checklists as $workName => $folderData) {
            $natureOfWorkId = $workIds[$workName];
            foreach ($folderData as $folderName => $docs) {
                $folderId = $folders[$folderName] ?? null;
                if ($folderId) {
                    foreach ($docs as $idx => $docName) {
                        DB::table('document_fields')->insert([
                            'folder_id' => $folderId,
                            'nature_of_work_id' => $natureOfWorkId,
                            'name' => $docName,
                            'required' => true,
                            'sort_order' => $idx + 1,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }
}
