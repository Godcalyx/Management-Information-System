<?php

namespace App\Exports;

use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\Grade;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class GradeExport implements FromView
{
    protected $gradeLevel;
    protected $quarter;

    public function __construct($gradeLevel, $quarter)
    {
        $this->gradeLevel = $gradeLevel;
        $this->quarter = $quarter;
    }

    public function view(): View
    {
        $subjects = Subject::where('grade_level', $this->gradeLevel)->get();
        $students = Enrollment::where('grade_level', $this->gradeLevel)->with(['user'])->get();

        foreach ($students as $student) {
            $grades = Grade::where('user_id', $student->user_id)
                ->where('quarter', $this->quarter)
                ->get();

            $total = 0;
            $count = 0;

            foreach ($subjects as $subject) {
                $grade = $grades->firstWhere('subject_id', $subject->id);
                $student->grades[$subject->id] = $grade;
                if ($grade && $grade->grade !== null) {
                    $total += $grade->grade;
                    $count++;
                }
            }

            $average = $count > 0 ? round($total / $count, 2) : null;
            $student->average = $average;

            // Determine remarks
            if ($average !== null) {
                if ($average >= 98) {
                    $student->remarks = 'With Highest Honors 🥇';
                } elseif ($average >= 95) {
                    $student->remarks = 'With High Honors 🥈';
                } elseif ($average >= 90) {
                    $student->remarks = 'With Honors 🥉';
                } elseif ($average >= 75) {
                    $student->remarks = 'Passed';
                } else {
                    $student->remarks = 'Failed';
                }
            } else {
                $student->remarks = 'Incomplete';
            }
        }

        return view('exports.grades', [
            'gradeLevel' => $this->gradeLevel,
            'quarter' => $this->quarter,
            'subjects' => $subjects,
            'students' => $students,
        ]);
    }
}
