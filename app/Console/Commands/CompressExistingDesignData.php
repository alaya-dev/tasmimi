<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Template;
use App\Models\ClientTemplate;
use App\Services\DesignDataService;
use Illuminate\Support\Facades\DB;

class CompressExistingDesignData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'design:compress-data {--dry-run : Show what would be compressed without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress existing design data to reduce database size';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Running in dry-run mode. No data will be modified.');
        }

        $this->info('Compressing Template design data...');
        $this->compressTemplateData($dryRun);

        $this->info('Compressing ClientTemplate design data...');
        $this->compressClientTemplateData($dryRun);

        $this->info('Design data compression completed!');
    }

    private function compressTemplateData($dryRun = false)
    {
        $templates = DB::table('templates')
            ->whereNotNull('design_data')
            ->where('design_data', '!=', '')
            ->get(['id', 'design_data', 'name']);

        $totalSavings = 0;
        $processed = 0;

        foreach ($templates as $template) {
            $originalSize = strlen($template->design_data);

            // Check if already compressed
            if (DesignDataService::isCompressed($template->design_data)) {
                continue;
            }

            try {
                $designData = json_decode($template->design_data, true);
                if (!$designData) {
                    continue;
                }

                $optimized = DesignDataService::optimizeDesignData($designData);
                $compressed = DesignDataService::compressDesignData($optimized);
                $newSize = strlen($compressed);
                $savings = $originalSize - $newSize;
                $totalSavings += $savings;

                $this->line(sprintf(
                    'Template "%s" (ID: %d): %s -> %s (saved %s)',
                    $template->name,
                    $template->id,
                    $this->formatBytes($originalSize),
                    $this->formatBytes($newSize),
                    $this->formatBytes($savings)
                ));

                if (!$dryRun) {
                    DB::table('templates')
                        ->where('id', $template->id)
                        ->update(['design_data' => $compressed]);
                }

                $processed++;
            } catch (\Exception $e) {
                $this->error("Error processing template {$template->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$processed} templates. Total savings: " . $this->formatBytes($totalSavings));
    }

    private function compressClientTemplateData($dryRun = false)
    {
        $clientTemplates = DB::table('client_templates')
            ->whereNotNull('design_data')
            ->where('design_data', '!=', '')
            ->get(['id', 'design_data', 'name']);

        $totalSavings = 0;
        $processed = 0;

        foreach ($clientTemplates as $clientTemplate) {
            $originalSize = strlen($clientTemplate->design_data);

            // Check if already compressed
            if (DesignDataService::isCompressed($clientTemplate->design_data)) {
                continue;
            }

            try {
                $designData = json_decode($clientTemplate->design_data, true);
                if (!$designData) {
                    continue;
                }

                $optimized = DesignDataService::optimizeDesignData($designData);
                $compressed = DesignDataService::compressDesignData($optimized);
                $newSize = strlen($compressed);
                $savings = $originalSize - $newSize;
                $totalSavings += $savings;

                $this->line(sprintf(
                    'ClientTemplate "%s" (ID: %d): %s -> %s (saved %s)',
                    $clientTemplate->name,
                    $clientTemplate->id,
                    $this->formatBytes($originalSize),
                    $this->formatBytes($newSize),
                    $this->formatBytes($savings)
                ));

                if (!$dryRun) {
                    DB::table('client_templates')
                        ->where('id', $clientTemplate->id)
                        ->update(['design_data' => $compressed]);
                }

                $processed++;
            } catch (\Exception $e) {
                $this->error("Error processing client template {$clientTemplate->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$processed} client templates. Total savings: " . $this->formatBytes($totalSavings));
    }

    private function formatBytes($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
