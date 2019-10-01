<?php

use App\Models\Site;
use App\Models\SubSite;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class SeedSitesTable extends Seeder
{
    public function run()
    {
        foreach (Excel::load(storage_path('Sites-Subsites-reps.xlsx'))->get() as $fileRow) {
            if ($fileRow['site'] != '' && $fileRow['representitve_name'] != "") {
                $site = Site::firstOrCreate(['title' => $fileRow['site'] ?? "", 'representative' => $fileRow['representitve_name'] ?? ""]);
                SubSite::create([
                    'site_id'           => $site->id,
                    'representative'    => $site->representative,
                    'title'             => $fileRow['sub_site'] ?? ""
                ]);
            }
        }
    }
}
