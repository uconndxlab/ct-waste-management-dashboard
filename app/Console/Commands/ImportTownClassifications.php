<?php

namespace App\Console\Commands;

use App\Models\TownClassification;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ImportTownClassifications extends Command
{
    protected $signature = 'import:town-classifications {file}';
    protected $description = 'Import town classification data from CSV';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            // Debug output to see what's being read
            $this->info("Processing: {$record['Municipality']} - Region Type: {$record['Region Type']}");
            
            TownClassification::updateOrCreate(
                ['municipality' => $record['Municipality']],
                [
                    'region_type' => $record['Region Type'],
                    'geographical_region' => $record['Geographical Region'],
                    'county' => $record['County']
                ]
            );
        }

        $this->info('Town classifications imported successfully');
    }
}