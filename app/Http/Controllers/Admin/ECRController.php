<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Subject;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ECRController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Expected subject count per grade level
        $subjectCounts = [
            7 => 9,
            8 => 10,
            9 => 10,
            10 => 10,
        ];

        $query = Enrollment::query()
            ->where('status', 'approved')
            ->with(['user', 'grades.subject']);

        // ✅ Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // ✅ Search by name or LRN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('lrn', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->get();

        // ✅ Filter only students with complete grades based on grade level
        $students = $enrollments->filter(function ($enrollment) use ($subjectCounts) {
            $gradeLevel = $enrollment->grade_level;
            $expectedSubjects = $subjectCounts[$gradeLevel] ?? 0;
            return $enrollment->grades->count() >= $expectedSubjects;
        })->map(function ($enrollment) {
            return (object) [
                'id' => $enrollment->user->id,
                'name' => $enrollment->user->name,
                'email' => $enrollment->user->email,
                'lrn' => $enrollment->user->lrn,
                'grade_level' => $enrollment->grade_level,
            ];
        });

        return view('admin.classrecord.index', compact('students'));
    }

    // ===========================
    // EXPORT FUNCTIONS BELOW
    // ===========================

    public function exportSingle($user_id)
    {
        $enrollments = Enrollment::with(['user', 'grades.subject'])
            ->where('user_id', $user_id)
            ->get();

        return $this->generateExcel($enrollments);
    }

    public function exportAll($grade_level)
    {
        $enrollments = Enrollment::with(['user', 'grades.subject'])
            ->where('grade_level', $grade_level)
            ->get();

        return $this->generateExcel($enrollments);
    }

    private function generateExcel($enrollments)
    {
        $templatePath = public_path('templates/template orig.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $pairIndex = 0;

        foreach ($enrollments->chunk(2) as $pair) {
            $offset = $pairIndex * 42;
            $pairIndex++;

            $this->fillCard($sheet, $pair[0], 'left', $offset);

            if (isset($pair[1])) {
                $this->fillCard($sheet, $pair[1], 'right', $offset);
            }

            if ($pairIndex < ceil($enrollments->count() / 2)) {
                $this->duplicateTemplateSection($sheet, $offset + 42);
            }
        }

        $fileName = 'Report_Cards_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/public/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function fillCard(Worksheet $sheet, $enrollment, $side = 'left', $offset = 0)
    {
        $user = $enrollment->user;
        $cols = $side === 'left'
            ? ['lrn' => 'F', 'name' => 'C', 'age' => 'H', 'sex' => 'K', 'grade' => 'H', 'curriculum' => 'C', 'sy' => 'C', 'section' => 'K', 'avg' => 'G', 'adviser' => 'C', 'principal' => 'G']
            : ['lrn' => 'AA', 'name' => 'V', 'age' => 'AA', 'sex' => 'AD', 'grade' => 'AA', 'curriculum' => 'V', 'sy' => 'V', 'section' => 'AD', 'avg' => 'AB', 'adviser' => 'X', 'principal' => 'AB'];

        $age = $user->birthdate ? Carbon::parse($user->birthdate)->age : '';
        $gwa = round($enrollment->grades->avg('final_grade'), 2);

        $sheet->setCellValue($cols['name'] . (8 + $offset), $user->name);
        $sheet->setCellValue($cols['age'] . (8 + $offset), $age);
        $sheet->setCellValue($cols['sex'] . (8 + $offset), $user->sex);
        $sheet->setCellValue($cols['grade'] . (9 + $offset), $enrollment->grade_level);
        $sheet->setCellValue($cols['curriculum'] . (9 + $offset), 'Science');
        $sheet->setCellValue($cols['sy'] . (10 + $offset), $enrollment->school_year);
        $sheet->setCellValue($cols['section'] . (9 + $offset), $enrollment->section);
        $sheet->setCellValue($cols['avg'] . (23 + $offset), $gwa);
        $sheet->setCellValue($cols['adviser'] . (35 + $offset), $enrollment->adviser ?? '');
        $sheet->setCellValue($cols['principal'] . (35 + $offset), $enrollment->principal ?? '');

        foreach ($enrollment->grades as $i => $grade) {
            $baseRow = 13 + $i + $offset;
            $subjectName = $grade->subject->name ?? '';
            $finalGrade = $grade->final_grade ?? '';

            if ($side === 'left') {
                $sheet->setCellValue('B' . $baseRow, $subjectName);
                $sheet->setCellValue('I' . $baseRow, $finalGrade);
            } else {
                $sheet->setCellValue('U' . $baseRow, $subjectName);
                $sheet->setCellValue('AB' . $baseRow, $finalGrade);
            }
        }
    }

    private function duplicateTemplateSection(Worksheet $sheet, $startRow)
    {
        for ($i = 1; $i <= 42; $i++) {
            $sheet->insertNewRowBefore($startRow + $i);
        }
    }
}
