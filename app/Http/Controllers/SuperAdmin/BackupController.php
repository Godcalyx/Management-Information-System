<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function create()
    {
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST', '127.0.0.1');

        $fileName = 'backup_' . $dbName . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $filePath = storage_path('app/' . $fileName);
        $mysqldumpPath = 'C:/xammpp/mysql/bin/mysqldump.exe';

        if (!File::exists($mysqldumpPath)) {
            return back()->with('error', 'mysqldump was not found on this machine.');
        }

        $command = "\"$mysqldumpPath\" -h $dbHost -u $dbUser";

        if (!empty($dbPass)) {
            $command .= " -p\"$dbPass\"";
        }

        $command .= " $dbName > \"$filePath\"";

        $output = [];
        $result = null;

        exec($command, $output, $result);

        if ($result !== 0 || !File::exists($filePath)) {
            return back()->with('error', 'Database backup failed. Check logs for details.');
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
