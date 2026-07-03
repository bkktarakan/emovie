<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function index()
    {
        $dir   = storage_path('backups');
        $files = [];
        if (is_dir($dir)) {
            foreach (array_reverse(glob($dir . '/*.sql')) as $f) {
                $files[] = ['name' => basename($f), 'size' => round(filesize($f) / 1024, 1), 'time' => filemtime($f)];
            }
        }
        return view('backup.index', compact('files'));
    }

    public function create()
    {
        Artisan::call('app:backup-database');
        ActivityLog::record('Backup', 'Database', 'Membuat backup database manual');
        return redirect()->route('backup.index')->with('success', 'Backup database berhasil dibuat.');
    }

    public function download(string $filename): BinaryFileResponse
    {
        $path = storage_path('backups/' . basename($filename));
        abort_unless(file_exists($path) && str_ends_with($filename, '.sql'), 404);
        return response()->download($path);
    }

    public function destroy(string $filename)
    {
        $path = storage_path('backups/' . basename($filename));
        if (file_exists($path)) unlink($path);
        return redirect()->route('backup.index')->with('success', 'File backup berhasil dihapus.');
    }
}
