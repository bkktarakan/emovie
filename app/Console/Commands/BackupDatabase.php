<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature   = 'app:backup-database';
    protected $description = 'Backup database MySQL ke file SQL';

    public function handle(): int
    {
        $dir = storage_path('backups');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $file     = $dir . '/backup_' . now()->format('Y-m-d_His') . '.sql';
        $db       = config('database.connections.mysql.database');
        $user     = config('database.connections.mysql.username');
        $pass     = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port');
        $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        $passFlag  = $pass ? "-p{$pass}" : '';
        $cmd = "\"{$mysqldump}\" -h{$host} -P{$port} -u{$user} {$passFlag} {$db}";

        exec($cmd, $output, $code);

        if ($code === 0) {
            file_put_contents($file, implode("\n", $output));
            $this->info("Backup berhasil: {$file}");
            return 0;
        }

        $this->error("Backup gagal (exit code: {$code}).");
        return 1;
    }
}
