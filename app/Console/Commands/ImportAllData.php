<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ImportAllData extends Command
{
    protected $signature = 'import:all-data';
    protected $description = 'Import all town, financial, and contact data from CSV files';

    public function handle()
    {
        $this->info('Starting import process...');

        // define file paths
        $townsFile = storage_path('app/towns.csv');
        $financialsFile = storage_path('app/financials.csv');
        $contactsFile = storage_path('app/contacts.csv');

        // run the ImportTowns command
        if (file_exists($townsFile)) {
            Artisan::call('import:towns', ['file' => $townsFile]);
            $this->info(Artisan::output());
        } else {
            $this->error("File not found: $townsFile");
        }

        // Run the ImportMunicipalityFinancialData command
        if (file_exists($financialsFile)) {
            Artisan::call('import:municipality-financial-data', ['file' => $financialsFile]);
            $this->info(Artisan::output());
        } else {
            $this->error("File not found: $financialsFile");
        }

        // run the ImportTownContacts command
        if (file_exists($contactsFile)) {
            Artisan::call('import:town_contacts', ['file' => $contactsFile]);
            $this->info(Artisan::output());
        } else {
            $this->error("File not found: $contactsFile");
        }

        $this->info('All imports completed successfully!');
    }
}
