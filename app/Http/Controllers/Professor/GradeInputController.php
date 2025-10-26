<!-- 
namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeInputController extends Controller
{
    public function index(Request $request)
    {
        $professorId = Auth::id();

        // Get all assigned subject-grade level combinations
        $assignments = DB::table('professor_subject_grade_levels')
            ->join('subjects', 'subjects.id', '=', 'professor_subject_grade_levels.subject_id')
            ->where('professor_subject_grade_levels.user_id', $professorId)
            ->select('subjects.id as subject_id', 'subjects.name as subject_name', 'professor_subject_grade_levels.grade_level')
            ->get();

        // If user selected a subject and grade level
        $subjectId = $request->input('subject_id');
        $gradeLevel = $request->input('grade_level');
        $quarter = $request->input('quarter') ?? '1'; // default to 1st quarter
        $schoolYear = $request->input('school_year') ?? '2024-2025';

        $students = collect();
        $existingGrades = collect();
        $selectedSubject = null;

        if ($subjectId && $gradeLevel) {
            $selectedSubject = Subject::find($subjectId);

            $students = User::where('role', 'student')
                ->where('grade_level', $gradeLevel)
                ->get();

            $existingGrades = Grade::where('subject_id', $subjectId)
                ->where('quarter', $quarter)
                ->where('school_year', $schoolYear)
                ->whereIn('user_id', $students->pluck('id'))
                ->get()
                ->keyBy('user_id');
        }

        return view('professor.grades.input', compact(
            'assignments', 'subjectId', 'gradeLevel', 'quarter', 'schoolYear', 'students', 'existingGrades', 'selectedSubject'
        ));
    }
} -->
