<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::where('name','=','CMS')->first();
        if($company == null){
            $company = new Company();
            $company->brand_id = 1;
            $company->logo_id = 1;
            $company->hero_image_id = 2;
            $company->favicon_id = 5;
            $company->name = 'CMS';
            $company->addess_line1 = 'Ahemedabad';
            $company->addess_line2 = 'Rajkot';
            $company->city = 'Ahemedabad';
            $company->state = 'Gujarat';
            $company->zipcode = '001536';
            $company->contact_person = 'Mr.patel';
            $company->designation = 'CEO';
            $company->email = 'cms@gmail.com';
            $company->primany_number = '0101010101';
            $company->alternate_number = '0202020202';
            $company->email_header_id = 4;
            $company->email_footer = '<div style="text-align:center;"> Copyright @ 2021. All Rights Reserved </div>';
            $company->api_key = 'cmscourier@2021';
            $company->theme_color = '54 105 227';
            $company->as3_bucket = '';
            $company->session_timeout = 60;
            $company->cash_limit = 20;
            $company->crtm = 20;
            $company->handling_charges = 20;
            $company->fastag = 20;
            $company->municipality_tax = 20;
            $company->tcs = 20;
            $company->pms = 20;
            $company->extended_warrenty = 20;
            $company->save();
        }
    }
}
