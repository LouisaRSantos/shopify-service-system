<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanupExportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exports:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired customer export Excel files';

    public function handle()
    {
        $exportPath = storage_path('app/exports');
        if (!File::exists($exportPath)) {
            $this->warn('Exports directory does not exist.');

            return Command::SUCCESS;
        }

        $retentionDays = 7;
        $files = File::files($exportPath);
        $deletedCount = 0;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'xlsx') {
                continue;
            }
            $lastModified = Carbon::createFromTimestamp(
                $file->getMTime()
            );
            if ($lastModified->diffInDays(now()) < $retentionDays) {
                continue;
            }
            File::delete($file->getPathname());
            $this->info(
                'Deleted: ' . $file->getFilename()
            );
            $deletedCount++;
        }
        if ($deletedCount === 0) {
            $this->info(
                'No expired export files found.'
            );
        }
        $this->info(
            'Cleanup complete.'
        );
        return Command::SUCCESS;
    }
}
