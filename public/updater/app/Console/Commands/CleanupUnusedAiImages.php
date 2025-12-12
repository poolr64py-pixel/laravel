<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupUnusedAiImages extends Command
{
    protected $signature = 'ai:cleanup 
                            {--hours=24 : Delete files older than X hours}
                            {--minutes=30 : Delete files older than X minutes}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Cleanup unused AI generated images from storage';

    public function handle()
    {
        $hours = (int) $this->option('hours');
        $minutes = (int) $this->option('minutes');
        $isDryRun = $this->option('dry-run');

        $cutoffTime = Carbon::now();
        
        if ($minutes > 0) {
            $cutoffTime->subMinutes($minutes);
            $this->info("Deleting files older than: {$minutes} minutes");
        } else {
            $cutoffTime->subHours($hours);
            $this->info("Deleting files older than: {$hours} hours");
        }

        $aiDirectories = [
            'thumbnail' => public_path('assets/img/properties/ai-generated/thumbnail'),
            'floor_plan' => public_path('assets/img/properties/ai-generated/floor_plan'),
            'video_poster' => public_path('assets/img/properties/ai-generated/video_poster'),
            'gallery' => public_path('assets/img/properties/ai-generated/gallery'),
        ];

        $totalStats = ['deleted' => 0, 'failed' => 0, 'size' => 0];

        foreach ($aiDirectories as $type => $directory) {
            if (!File::exists($directory)) {
                $this->warn("Directory not found: {$type}");
                continue;
            }

            $stats = $this->processDirectory($directory, $cutoffTime, $isDryRun);
            $totalStats['deleted'] += $stats['deleted'];
            $totalStats['failed'] += $stats['failed'];
            $totalStats['size'] += $stats['size'];
        }

        $this->cleanupEmptyDirectories($aiDirectories, $isDryRun);
        $this->showCleanupResults($totalStats, $isDryRun);

        return Command::SUCCESS;
    }

    private function processDirectory(string $directory, Carbon $cutoffTime, bool $isDryRun): array
    {
        $stats = ['deleted' => 0, 'failed' => 0, 'size' => 0];
        $files = File::files($directory);

        if (empty($files)) {
            return $stats;
        }

        foreach ($files as $file) {
            $fileModifiedTime = Carbon::createFromTimestamp($file->getMTime());

            if ($fileModifiedTime->lt($cutoffTime)) {
                $stats = $this->processFileDeletion($file, $stats, $isDryRun);
            }
        }

        return $stats;
    }

    private function processFileDeletion($file, array $stats, bool $isDryRun): array
    {
        $fileSize = $file->getSize();
        $fileName = $file->getFilename();
        $filePath = $file->getPathname();

        if ($isDryRun) {
            $this->line("Would delete: {$fileName}");
            $stats['size'] += $fileSize;
            return $stats;
        }

        try {
            if (File::delete($filePath)) {
                $stats['deleted']++;
                $stats['size'] += $fileSize;
                $this->info("Deleted: {$fileName}");
            } else {
                $stats['failed']++;
                $this->error("Failed to delete: {$fileName}");
            }
        } catch (\Exception $e) {
            $stats['failed']++;
            $this->error("Error deleting {$fileName}");
        }

        return $stats;
    }

    private function cleanupEmptyDirectories(array $directories, bool $isDryRun): void
    {
        foreach ($directories as $type => $directory) {
            if (!File::exists($directory) || !File::isDirectory($directory)) {
                continue;
            }

            $files = File::files($directory);
            $subdirs = File::directories($directory);

            if (empty($files) && empty($subdirs)) {
                if (!$isDryRun) {
                    try {
                        File::deleteDirectory($directory);
                    } catch (\Exception $e) {
                        // Silent fail for directory deletion
                    }
                }
            }
        }
    }

    private function showCleanupResults(array $stats, bool $isDryRun): void
    {
        $this->info("Cleanup Completed!");
        $this->info("Files deleted: {$stats['deleted']}");
        $this->info("Failed deletions: {$stats['failed']}");
        $this->info("Space freed: " . $this->formatBytes($stats['size']));

        if ($isDryRun) {
            $this->info("This was a dry run. Use without --dry-run to actually delete files.");
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
