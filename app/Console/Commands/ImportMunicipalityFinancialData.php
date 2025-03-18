<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MunicipalityFinancialData;
use League\Csv\Reader;

class ImportMunicipalityFinancialData extends Command
{
    protected $signature = 'import:municipality-financial-data {file}';
    protected $description = 'Import municipality financial data from a CSV file';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0); // Set CSV header offset to 0

        foreach ($csv as $record) {
            MunicipalityFinancialData::updateOrCreate(
                ['municipality' => $record['Municipality']],
                [
                    'time_period' => $record['Time Period'] ?? null,
                    'link' => $record['link'] ?? null,
                    'population' => is_numeric(str_replace(',', '', $record['Town population (2022)'] ?? '')) 
                        ? str_replace(',', '', $record['Town population (2022)']) 
                        : null,
                    'size' => is_numeric($record['Town size (Square miles) (2010)'] ?? '') 
                        ? $record['Town size (Square miles) (2010)'] 
                        : null,
                    'notes' => $record['notes'] ?? null,
                ]
            );
        }

        $this->info("Municipality financial data imported successfully!");
    }
}
