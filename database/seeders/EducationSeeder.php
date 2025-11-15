<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\SessionYear;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Subject;
use App\Models\SubjectChapters;
use App\Models\ClassSection;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Common info
        $tenet_id = '2';
        $tenet_name = 'Roland Institute of Technology';
        $university_id = 1;
        $university_name = 'BPUT';
        $emp_id = 'EMP001';

        // 3 Session Years
        $sessionYears = [
            ['name' => '2023-2024', 'start' => '2023-06-01', 'end' => '2024-05-31'],
            ['name' => '2024-2025', 'start' => '2024-06-01', 'end' => '2025-05-31'],
            ['name' => '2025-2026', 'start' => '2025-06-01', 'end' => '2026-05-31'],
        ];

        foreach ($sessionYears as $yearData) {
            $sessionYear = SessionYear::create([
                'tenet_id' => $tenet_id,
                'tenet_name' => $tenet_name,
                'university_id' => $university_id,
                'university_name' => $university_name,
                'emp_id' => $emp_id,
                'name' => $yearData['name'],
                'start_date' => $yearData['start'],
                'end_date' => $yearData['end'],
                'is_active' => 1,
            ]);

            $yearSuffix = substr($yearData['start'], 2, 2); // e.g. 25 from 2025

            // 10 Courses per Session
            for ($c = 1; $c <= 10; $c++) {
                $courseName = $faker->randomElement([
                    'BCA', 'BBA', 'MBA', 'MCA', 'B.Tech',
                    'M.Tech', 'B.Sc', 'M.Sc', 'B.Com', 'M.Com'
                ]);

                $randomDigits = rand(1000, 9999);
                $courseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $courseName), 0, 3))
                    . $yearSuffix
                    . $randomDigits;

                $course = Course::create([
                    'tenet_id' => $tenet_id,
                    'tenet_name' => $tenet_name,
                    'university_id' => $university_id,
                    'university_name' => $university_name,
                    'emp_id' => $emp_id,
                    'session_year_id' => $sessionYear->id,
                    'course_name' => $courseName . " $c",
                    'course_code' => $courseCode,
                    'is_active' => 1,
                    'description' => "$courseName Course for {$yearData['name']}",
                ]);

                // 3 Classes per Course
                $classNames = ['1st Year', '2nd Year', '3rd Year'];
                $classNumber = 1;

                foreach ($classNames as $className) {
                    $classRandom = rand(1000, 9999);
                    $classCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $course->course_name), 0, 3))
                        . $yearSuffix
                        . str_pad($classNumber, 2, '0', STR_PAD_LEFT)
                        . $classRandom;

                    $class = CourseClass::create([
                        'tenet_id' => $tenet_id,
                        'tenet_name' => $tenet_name,
                        'university_id' => $university_id,
                        'university_name' => $university_name,
                        'emp_id' => $emp_id,
                        'session_year_id' => $sessionYear->id,
                        'course_id' => $course->id,
                        'class_name' => $className,
                        'class_code' => $classCode,
                        'is_active' => 1,
                    ]);

                    $classNumber++;

                    // 3 Sections per Class (A, B, C) with RANDOM section codes
                    $sections = ['A', 'B', 'C'];

                    foreach ($sections as $sectionName) {
                        $randomSectionCode = strtoupper($faker->lexify('????')); // 4 random letters
                        $randomDigits = rand(100, 999);

                        $sectionCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $course->course_name), 0, 3))
                            . $yearSuffix
                            . $sectionName
                            . $randomSectionCode
                            . $randomDigits;

                        ClassSection::create([
                            'tenet_id' => $tenet_id,
                            'tenet_name' => $tenet_name,
                            'university_id' => $university_id,
                            'university_name' => $university_name,
                            'emp_id' => $emp_id,
                            'session_year_id' => $sessionYear->id,
                            'course_id' => $course->id,
                            'course_class_id' => $class->id,
                            'section_name' => "Section $sectionName",
                            'section_code' => $sectionCode,
                            'is_active' => 1,
                        ]);
                    }

                    // 3 Subjects per Class
                    $subjects = [
                        'Mathematics',
                        'Physics',
                        'C Programming',
                        'Database Systems',
                        'Operating Systems',
                        'Software Engineering',
                        'English Communication',
                        'Computer Networks',
                        'Data Structures',
                        'Artificial Intelligence'
                    ];

                    $randomSubjects = $faker->randomElements($subjects, 3);
                    $subjectCounter = 1;

                    foreach ($randomSubjects as $subjectName) {
                        $hasPracticals = $faker->boolean();
                        $theoryMark = $hasPracticals ? 70 : 100;
                        $practicalMark = $hasPracticals ? 30 : 0;
                        $fullMark = $theoryMark + $practicalMark;

                        $subjectRandom = rand(1000, 9999);
                        $subjectCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $course->course_name), 0, 3))
                            . $yearSuffix
                            . strtoupper(substr($subjectName, 0, 3))
                            . str_pad($subjectCounter, 2, '0', STR_PAD_LEFT)
                            . $subjectRandom;

                        $subject = Subject::create([
                            'tenet_id' => $tenet_id,
                            'tenet_name' => $tenet_name,
                            'university_id' => $university_id,
                            'university_name' => $university_name,
                            'session_year_id' => $sessionYear->id,
                            'course_id' => $course->id,
                            'course_class_id' => $class->id,
                            'subject_name' => $subjectName,
                            'subject_code' => $subjectCode,
                            'has_practicals' => $hasPracticals ? 'true' : 'false',
                            'theory_mark' => $theoryMark,
                            'practical_mark' => $practicalMark,
                            'full_mark' => $fullMark,
                            'is_active' => 1,
                        ]);

                        $subjectCounter++;

                        // 10 Chapters per Subject
                        for ($ch = 1; $ch <= 10; $ch++) {
                            SubjectChapters::create([
                                'tenet_id' => $tenet_id,
                                'tenet_name' => $tenet_name,
                                'university_id' => $university_id,
                                'university_name' => $university_name,
                                'emp_id' => $emp_id,
                                'session_year_id' => $sessionYear->id,
                                'course_id' => $course->id,
                                'course_class_id' => $class->id,
                                'subject_id' => $subject->id,
                                'chapter_name' => "Chapter $ch: " . ucfirst($faker->words(3, true)),
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('Education Data Seeded Successfully');
    }
}