<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Advisory;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ECRController extends Controller
{
    private $subjectOrder = [
        1 => [ // Grade 7
            'Filipino 1','English 1','Mathematics 1','Science 1','Araling Panlipunan 1',
            'Music, Arts, Physical Education & Health 1',
            'Technology and Livelihood Education (Agri-Fishery Arts) 1',
            'Edukasyon sa Pagpapakatao 1','Science 2 (Environmental Science)',
        ],
        2 => [ // Grade 8
            'Filipino 2','English 2','Mathematics 2','Science 2','Araling Panlipunan 2',
            'Music, Arts, Physical Education & Health 2',
            'Technology and Livelihood Education (Home Economics) 2',
            'Edukasyon sa Pagpapakatao 2','Advanced Algebra','Introduction to research',
        ],
        3 => [ // Grade 9
            'Filipino 3','English 3','Mathematics 3','Science 3','Araling Panlipunan 3',
            'Music, Arts, Physical Education & Health 3',
            'Technology and Livelihood Education (ICT) 3',
            'Edukasyon sa Pagpapakatao 3','Statistics','Outline and Experimental Design',
        ],
        4 => [ // Grade 10
            'Filipino 4','English 4','Mathematics 4','Science 4','Araling Panlipunan 4',
            'Music, Arts, Physical Education & Health 4',
            'Technology and Livelihood Education (ICT) 4',
            'Edukasyon sa Pagpapakatao 4','Advanced Biology','Applied Research',
        ],
    ];

    /* ===========================================================
       INDEX
    =========================================================== */
    public function index(Request $request)
    {
        $latestEnrollmentsQuery = Enrollment::with([
            'user',
            'grades' => fn($q) => $q->where('status', 'approved')->with('subject'),
            'gradeLevel'
        ])
        ->where('status', 'approved')
        ->where(function($query) {
            $query->whereNull('completion_status')
                  ->orWhere('completion_status', '<>', 'graduated');
        })
        ->whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))->from('enrollments')->groupBy('user_id');
        });

        if ($request->filled('grade_level')) {
            $latestEnrollmentsQuery->where('grade_level_id', $request->grade_level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $latestEnrollmentsQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name','like',"%{$search}%")
                  ->orWhere('lrn','like',"%{$search}%");
            });
        }

        $enrollments = $latestEnrollmentsQuery->get();

        $students = $enrollments->map(function ($e) {
            return (object)[
                'id' => $e->user->id,
                'name' => $e->user->name,
                'email' => $e->user->email,
                'lrn' => $e->user->lrn,
                'grade_level' => $e->gradeLevel->name ?? '',
            ];
        });

        return view('admin.classrecord.index', [
            'students' => $students,
            'selectedGrade' => $request->grade_level ?? '',
            'searchTerm' => $request->search ?? '',
        ]);
    }

    /* ===========================================================
       EXPORT SINGLE STUDENT
    =========================================================== */
    public function exportSingle($user_id)
    {
        $enrollments = Enrollment::with([
            'user',
            'grades' => fn($q) => $q->where('status', 'approved')->with('subject'),
            'attendances',
            'gradeLevel'
        ])
        ->where('user_id', $user_id)
        ->where(function($query) {
            $query->whereNull('completion_status')
                  ->orWhere('completion_status', '<>', 'graduated');
        })
        ->whereIn('id', function($q) use ($user_id) {
            $q->select(DB::raw('MAX(id)'))
              ->from('enrollments')
              ->where('user_id', $user_id)
              ->groupBy('user_id');
        })
        ->get();

        return $this->generateExcel($enrollments);
    }

    /* ===========================================================
       EXPORT ALL STUDENTS
    =========================================================== */
    public function exportAll(Request $request)
    {
        $enrollments = Enrollment::with([
            'user',
            'grades' => fn($q) => $q->where('status', 'approved')->with('subject'),
            'attendances',
            'gradeLevel'
        ])
        ->where('status','approved')
        ->where(function($query) {
            $query->whereNull('completion_status')
                  ->orWhere('completion_status', '<>', 'graduated');
        })
        ->whereIn('id', function($q) {
            $q->select(DB::raw('MAX(id)'))
              ->from('enrollments')
              ->groupBy('user_id');
        })
        ->get();

        return $this->generateExcel($enrollments);
    }

    /* ===========================================================
       EXCEL GENERATION
    =========================================================== */
    private function generateExcel($enrollments)
    {
        try {
            $spreadsheet = IOFactory::load(public_path('templates/LSHS_Report Card Form-138.xlsx'));
            $spreadsheet->getCalculationEngine()->disableCalculationCache();
            
            $templateSheet = $spreadsheet->getSheet(0);

            foreach ($enrollments->chunk(2) as $pairIndex => $pair) {
                $newSheet = $templateSheet->copy();
                $newSheet->setTitle('Pair ' . ($pairIndex + 1));
                $spreadsheet->addSheet($newSheet);

                $students = $pair->values();
                $left = $students->get(0);
                $right = $students->get(1);

                if ($left) $this->fillCard($newSheet, $left, 'left', 0);
                if ($right) $this->fillCard($newSheet, $right, 'right', 0);
            }

            if ($spreadsheet->getSheetCount() > 1) {
                $spreadsheet->removeSheetByIndex(0);
            }

            $filePath = storage_path('app/public/Report_Cards_' . now()->format('Ymd_His') . '.xlsx');
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save($filePath);

            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Excel generation failed: ' . $e->getMessage());
            return back()->withErrors('Export failed. Check logs for details.');
        }
    }

    /* ===========================================================
       FILL CARD
    =========================================================== */
    private function fillCard(Worksheet $sheet, $enrollment, $side, int $colOffset)
    {
        $user = $enrollment->user;
        $principal = auth()->user()->name ?? '';

        $advisory = \App\Models\Advisory::where('grade_level_id', $enrollment->gradeLevel->id)->first();
        $adviser = optional(\App\Models\User::find($advisory->user_id ?? null))->name ?? '';

        $cols = $side === 'left'
            ? ['lrn'=>'K','name'=>'C','curriculum'=>'C','sy'=>'C','age'=>'L','sex'=>'O','grade'=>'L','avg'=>'K','adviser'=>'A','principal'=>'I','subject'=>'B','q1'=>'G','q2'=>'H','q3'=>'I','q4'=>'J','final'=>'K','remarks'=>'M','month'=>['C','D','E','F','G','H','I','J','K','L','M','N'],'school'=>['C','D','E','F','G','H','I','J','K','L','M','N'],'present'=>['C','D','E','F','G','H','I','J','K','L','M','N'],'tardy'=>['C','D','E','F','G','H','I','J','K','L','M','N'],'total_school'=>'O','total_present'=>'O','total_tardy'=>'O']
            : ['lrn'=>'AB','name'=>'T','curriculum'=>'T','sy'=>'T','age'=>'AC','sex'=>'AF','grade'=>'AC','avg'=>'AB','adviser'=>'R','principal'=>'Z','subject'=>'S','q1'=>'X','q2'=>'Y','q3'=>'Z','q4'=>'AA','final'=>'AB','remarks'=>'AD','month'=>['T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE'],'school'=>['T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE'],'present'=>['T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE'],'tardy'=>['T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE'],'total_school'=>'AF','total_present'=>'AF','total_tardy'=>'AF'];

        $setBlack = function($cell, $value) {
            $cell->setValue($value);
            $cell->getStyle()->getFont()->getColor()->setRGB(Color::COLOR_BLACK);
        };

        $age = $enrollment->birthdate ? Carbon::parse($enrollment->birthdate)->age : '';
        $setBlack($sheet->getCell($this->col($cols['lrn'],$colOffset).'1'), $user->lrn ?? '');
        $setBlack($sheet->getCell($this->col($cols['name'],$colOffset).'8'), $user->name ?? '');
        $setBlack($sheet->getCell($this->col($cols['curriculum'],$colOffset).'9'), $enrollment->curriculum ?? 'Science');
        $setBlack($sheet->getCell($this->col($cols['sy'],$colOffset).'10'), $enrollment->school_year ?? '');
        $setBlack($sheet->getCell($this->col($cols['age'],$colOffset).'8'), $age);
        $setBlack($sheet->getCell($this->col($cols['sex'],$colOffset).'8'), $enrollment->sex ?? '');
        $setBlack($sheet->getCell($this->col($cols['grade'],$colOffset).'9'), $enrollment->gradeLevel->name ?? '');
        $setBlack($sheet->getCell($this->col($cols['adviser'],$colOffset).'34'), $adviser);
        $setBlack($sheet->getCell($this->col($cols['principal'],$colOffset).'34'), $principal);
        $setBlack($sheet->getCell($this->col($cols['principal'],$colOffset).'40'), $principal);

        // ====== Subjects & Grades ======
        $gradesBySubject = $enrollment->grades->groupBy('subject_id');
        $orderedSubjects = $this->subjectOrder[$enrollment->gradeLevel->id] ?? [];

        foreach ($orderedSubjects as $i => $subjectName) {
            $row = 14 + $i;

            $grade = $enrollment->grades->firstWhere('subject.name', $subjectName);
            if (!$grade) {
                $setBlack($sheet->getCell($this->col($cols['subject'],$colOffset).$row), $subjectName);
                continue;
            }

            $q1 = $gradesBySubject[$grade->subject_id]->firstWhere('quarter',1)->grade ?? '';
            $q2 = $gradesBySubject[$grade->subject_id]->firstWhere('quarter',2)->grade ?? '';
            $q3 = $gradesBySubject[$grade->subject_id]->firstWhere('quarter',3)->grade ?? '';
            $q4 = $gradesBySubject[$grade->subject_id]->firstWhere('quarter',4)->grade ?? '';
            $final = collect([$q1,$q2,$q3,$q4])->filter()->avg();

            $setBlack($sheet->getCell($this->col($cols['subject'],$colOffset).$row), $subjectName);
            $setBlack($sheet->getCell($this->col($cols['q1'],$colOffset).$row), $q1);
            $setBlack($sheet->getCell($this->col($cols['q2'],$colOffset).$row), $q2);
            $setBlack($sheet->getCell($this->col($cols['q3'],$colOffset).$row), $q3);
            $setBlack($sheet->getCell($this->col($cols['q4'],$colOffset).$row), $q4);
            $setBlack($sheet->getCell($this->col($cols['final'],$colOffset).$row), $final ? round($final,2) : '');
            $setBlack($sheet->getCell($this->col($cols['remarks'],$colOffset).$row), $final >= 75 ? 'Passed' : 'Failed');
        }

        // General Average
        $finalRatings = collect($gradesBySubject)->map(function($grades) {
            return collect([1,2,3,4])->map(fn($q) => $grades->firstWhere('quarter',$q)->grade ?? null)->filter()->avg();
        })->filter();

        if ($finalRatings->count()) {
            $ga = ceil($finalRatings->avg());
            $setBlack($sheet->getCell($this->col($cols['final'],$colOffset).'24'), $ga);
        }

        // Attendance
        $monthRow = 26; $schoolRow = 27; $presentRow = 28; $tardyRow = 29;
        $months = ['Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar','Apr','May','Jun'];
        $map = ['Aug'=>'August','Sep'=>'September','Oct'=>'October','Nov'=>'November','Dec'=>'December','Jan'=>'January','Feb'=>'February','Mar'=>'March','Apr'=>'April','May'=>'May','Jun'=>'June'];
        $ts=$tp=$tt=0;

        foreach ($months as $i=>$m) {
            $att = $enrollment->attendances->firstWhere('month',$map[$m]);
            $s = $att->days_of_school ?? 0;
            $p = $att->days_present ?? 0;
            $t = $att->times_tardy ?? 0;

            $setBlack($sheet->getCell($this->col($cols['month'][$i],$colOffset).$monthRow), $m);
            $setBlack($sheet->getCell($this->col($cols['school'][$i],$colOffset).$schoolRow), $s);
            $setBlack($sheet->getCell($this->col($cols['present'][$i],$colOffset).$presentRow), $p);
            $setBlack($sheet->getCell($this->col($cols['tardy'][$i],$colOffset).$tardyRow), $t);

            $ts+=$s; $tp+=$p; $tt+=$t;
        }

        $setBlack($sheet->getCell($this->col($cols['total_school'],$colOffset).$schoolRow), $ts);
        $setBlack($sheet->getCell($this->col($cols['total_present'],$colOffset).$presentRow), $tp);
        $setBlack($sheet->getCell($this->col($cols['total_tardy'],$colOffset).$tardyRow), $tt);
    }

    private function col(string $col, int $offset): string
    {
        return Coordinate::stringFromColumnIndex(
            Coordinate::columnIndexFromString($col) + $offset
        );
    }
}
