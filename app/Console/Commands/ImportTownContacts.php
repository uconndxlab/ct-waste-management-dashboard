<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OverallTownInfo;
use League\Csv\Reader;

class ImportTownContacts extends Command
{
    protected $signature = 'import:town_contacts {file}';
    protected $description = 'Import town contact information from a CSV file';

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
            OverallTownInfo::updateOrCreate(
                ['municipality' => trim($record['Municipality'])],
                [
                    'department' => $record['Department'] ?? null,
                    'contact_1' => $record['Contact 1'] ?? null,
                    'title_1' => $record['Title 1'] ?? null,
                    'phone_1' => $record['Phone 1'] ?? null,
                    'email_1' => $record['Email 1'] ?? null,
                    'contact_2' => $record['Contact 2'] ?? null,
                    'title_2' => $record['Title 2'] ?? null,
                    'phone_2' => $record['Phone 2'] ?? null,
                    'email_2' => $record['Email 2'] ?? null,
                    'notes' => $record['Notes'] ?? null,
                    'other_useful_notes' => $record['Other Useful Notes'] ?? null,
                ]
            );
        }
    }
}
