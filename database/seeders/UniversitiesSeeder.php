<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Education\University;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UniversitiesSeeder extends Seeder
{
    public function run()
    {
        $universities = [

            // Universities
            [
                'org_name' => 'Utkal University',
                'state' => 'Odisha',
                'district' => 'Khordha',
                'city' => 'Bhubaneswar',
                'email_id' => 'info@utkaluniversity.ac.in',
                'alt_email_id' => 'contact@utkaluniversity.ac.in',
                'phone_no' => '0674-2567940',
                'alternate_no' => '0674-2567850',
                'address' => 'Vani Vihar, Bhubaneswar, Odisha - 751004',
                'website_url' => 'https://www.utkaluniversity.ac.in',
                'org_logo' => null,
            ],

            [
                'org_name' => 'Sambalpur University',
                'state' => 'Odisha',
                'district' => 'Sambalpur',
                'city' => 'Burla',
                'email_id' => 'registrar@suniv.ac.in',
                'alt_email_id' => 'info@suniv.ac.in',
                'phone_no' => '0663-2430157',
                'alternate_no' => '0663-2432188',
                'address' => 'Jyoti Vihar, Sambalpur, Odisha - 768019',
                'website_url' => 'https://www.suniv.ac.in',
                'org_logo' => null,
            ],

            [
                'org_name' => 'Ravenshaw University',
                'state' => 'Odisha',
                'district' => 'Cuttack',
                'city' => 'Cuttack',
                'email_id' => 'registrar@ravenshawuniversity.ac.in',
                'alt_email_id' => 'info@ravenshawuniversity.ac.in',
                'phone_no' => '0671-2510060',
                'alternate_no' => '0671-2510304',
                'address' => 'College Square, Cuttack, Odisha - 753003',
                'website_url' => 'https://www.ravenshawuniversity.ac.in',
                'org_logo' => null,
            ],

            [
                'org_name' => 'Berhampur University',
                'state' => 'Odisha',
                'district' => 'Ganjam',
                'city' => 'Berhampur',
                'email_id' => 'registrar@buodisha.edu.in',
                'alt_email_id' => 'info@buodisha.edu.in',
                'phone_no' => '0680-2227333',
                'alternate_no' => '0680-2227421',
                'address' => 'Bhanja Bihar, Berhampur, Odisha - 760007',
                'website_url' => 'https://www.buodisha.edu.in',
                'org_logo' => null,
            ],

            [
                'org_name' => 'Odisha University of Agriculture and Technology (OUAT)',
                'state' => 'Odisha',
                'district' => 'Khordha',
                'city' => 'Bhubaneswar',
                'email_id' => 'registrar@ouat.ac.in',
                'alt_email_id' => 'info@ouat.ac.in',
                'phone_no' => '0674-2562360',
                'alternate_no' => '0674-2562364',
                'address' => 'Suryanagar, Bhubaneswar, Odisha - 751003',
                'website_url' => 'https://www.ouat.nic.in',
                'org_logo' => null,
            ],

            // Boards
            [
                'org_name' => 'Central Board of Secondary Education (CBSE)',
                'state' => 'Delhi',
                'district' => 'New Delhi',
                'city' => 'New Delhi',
                'email_id' => 'info@cbse.gov.in',
                'alt_email_id' => 'support@cbse.gov.in',
                'phone_no' => '011-22509256',
                'alternate_no' => '011-22509257',
                'address' => 'Shiksha Kendra, 2, Community Centre, Preet Vihar, Delhi - 110092',
                'website_url' => 'https://www.cbse.gov.in',
                'org_logo' => null,
            ],

            [
                'org_name' => 'Council of Higher Secondary Education (CHSE), Odisha',
                'state' => 'Odisha',
                'district' => 'Khordha',
                'city' => 'Bhubaneswar',
                'email_id' => 'info@chseodisha.nic.in',
                'alt_email_id' => 'support@chseodisha.nic.in',
                'phone_no' => '0674-2300099',
                'alternate_no' => '0674-2300120',
                'address' => 'C-2, Prajnapitha, Samantapur, Bhubaneswar - 751013',
                'website_url' => 'https://www.chseodisha.nic.in',
                'org_logo' => null,
            ],

            [
                'org_name' => 'Board of Secondary Education (BSE), Odisha / HSE Board',
                'state' => 'Odisha',
                'district' => 'Cuttack',
                'city' => 'Cuttack',
                'email_id' => 'info@bseodisha.ac.in',
                'alt_email_id' => 'support@bseodisha.ac.in',
                'phone_no' => '0671-2415460',
                'alternate_no' => '0671-2415428',
                'address' => 'Bajrakabati Road, Cuttack, Odisha - 753001',
                'website_url' => 'https://www.bseodisha.ac.in',
                'org_logo' => null,
            ],

        ];

        foreach ($universities as $uni) {
            University::create($uni);
        }
    }
}