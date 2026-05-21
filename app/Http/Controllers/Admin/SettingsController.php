<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use App\Models\GradeLevel;
use App\Models\Subject;

class SettingsController extends Controller
{
    /**
     * Show Settings Page with General, Security, Backup, Academic (Grade Levels & Subjects)
     */
    public function index()
    {
        $grade_levels = GradeLevel::orderBy('order')->get();
        $subjects = Subject::with('gradeLevels')->orderBy('name')->get();

        return view('admin.settings.index', compact('grade_levels', 'subjects'));
    }

    // -----------------------------
    // Update General Settings
    // -----------------------------
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        // Save school name
        Setting::updateOrCreate(
            ['key' => 'school_name'],
            ['value' => $request->school_name]
        );

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            $file = $request->file('school_logo');
            $path = $file->store('logos', 'public');

            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'school_logo')->first()?->value;
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Save new logo path
            Setting::updateOrCreate(
                ['key' => 'school_logo'],
                ['value' => $path]
            );
        }

        return redirect()->back()->with('success', 'General settings updated successfully.');
    }

    // -----------------------------
    // Backup Database
    // -----------------------------
   public function backupDatabase()
{
    $dbName = env('DB_DATABASE');
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');
    $dbHost = env('DB_HOST', '127.0.0.1');

    $fileName = 'backup_' . $dbName . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
    $filePath = storage_path('app/' . $fileName);

    // XAMPP path (Windows)
    $mysqldumpPath = 'C:/xammpp/mysql/bin/mysqldump.exe';

    // Build command
    $command = "\"$mysqldumpPath\" -h $dbHost -u $dbUser";
    if (!empty($dbPass)) {
        $command .= " -p\"$dbPass\"";
    }
    $command .= " $dbName > \"$filePath\"";

    $output = [];
    $result = null;

    exec($command, $output, $result);

    // Log details for debugging
    \Log::info('Backup command executed', [
        'command' => $command,
        'result' => $result,
        'output' => $output,
        'file_exists' => file_exists($filePath),
    ]);

    if ($result !== 0 || !file_exists($filePath)) {
        return back()->with('error', 'Database backup failed. Check logs for details.');
    }

    return response()->download($filePath)->deleteFileAfterSend(true);
}



    // -----------------------------
    // Update Security Settings
    // -----------------------------
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'security_min_password_length' => 'required|integer|min:6',
            'security_require_special_char' => 'required|boolean',
            'security_session_timeout' => 'required|integer|min:5',
        ]);

        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Security settings updated successfully.');
    }
}
