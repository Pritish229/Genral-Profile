<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Education\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
       
        $bca = Course::create([
            'course_name' => 'BCA',
            'course_code' => 'BCA',
            'parent_id'   => null,
            'is_parent'   => 'false',
            'is_active'   => true,
        ]);

        $mca = Course::create([
            'course_name' => 'MCA',
            'course_code' => 'MCA',
            'parent_id'   => null,
            'is_parent'   => 'true',
            'is_active'   => true,
        ]);

        $btech = Course::create([
            'course_name' => 'BTECH',
            'course_code' => 'BTECH',
            'parent_id'   => null,
            'is_parent'   => 'true',
            'is_active'   => true,
        ]);

        $mba = Course::create([
            'course_name' => 'MBA',
            'course_code' => 'MBA',
            'parent_id'   => null,
            'is_parent'   => 'true',
            'is_active'   => true,
        ]);

        $btechChildren = [
            ['CIVIL ENGINEERING', 'CIV'],
            ['ELECTRICAL ENGINEERING', 'EEE'],
            ['MECHANICAL ENGINEERING', 'MECH'],
            ['COMPUTER SCIENCE ENGINEERING', 'CSE'],
        ];

        foreach ($btechChildren as $c) {
            Course::create([
                'course_name' => $c[0],
                'course_code' => $c[1],
                'parent_id'   => $btech->id,
                'is_parent'   => 'false',
                'is_active'   => true,
            ]);
        }

        $mbaChildren = [
            ['HR MANAGEMENT', 'MBA-HR'],
            ['MARKETING', 'MBA-MKT'],
            ['FINANCE', 'MBA-FIN']
        ];

        foreach ($mbaChildren as $c) {
            Course::create([
                'course_name' => $c[0],
                'course_code' => $c[1],
                'parent_id'   => $mba->id,
                'is_parent'   => 'false',
                'is_active'   => true,
            ]);
        }

        $mcaChildren = [
            ['DATA SCIENCE', 'MCA-DS'],
            ['NETWORK SECURITY', 'MCA-NS']
        ];

        foreach ($mcaChildren as $c) {
            Course::create([
                'course_name' => $c[0],
                'course_code' => $c[1],
                'parent_id'   => $mca->id,
                'is_parent'   => 'false',
                'is_active'   => true,
            ]);
        }

    
        $independent = [
            ['BBA', 'BBA'],
            ['BSc Computer Science', 'BSC-CS'],
            ['BCom', 'BCOM'],
            ['MSc IT', 'MSC-IT'],
            ['Diploma in Animation', 'D-ANIM'],
            ['Diploma in Computer Applications', 'DCA']
        ];

        foreach ($independent as $c) {
            Course::create([
                'course_name' => $c[0],
                'course_code' => $c[1],
                'parent_id'   => null,
                'is_parent'   => 'false',
                'is_active'   => true,
            ]);
        }
    }
}
