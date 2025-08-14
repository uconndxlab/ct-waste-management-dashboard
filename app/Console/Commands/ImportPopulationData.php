<?php

namespace App\Console\Commands;

use App\Models\Population;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportPopulationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:population-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import population data from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvFile = storage_path('app/populations.csv');
        
        if (!file_exists($csvFile)) {
            $this->error('populations.csv file not found in project root');
            return 1;
        }

        $this->info('Starting population data import...');

        // Clear existing data
        DB::table('populations')->truncate();

        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle); // Skip header row
        
        $years = array_slice($header, 1); // Get years from header (2020, 2021, etc.)
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $municipality = $row[0];
            
            // Process each year's population data
            for ($i = 1; $i < count($row); $i++) {
                $populationValue = $row[$i];
                $year = $years[$i - 1];
                
                // Clean the population value - remove quotes and commas
                $cleanPopulation = str_replace(['"', ','], '', $populationValue);
                $cleanPopulation = (int) $cleanPopulation;
                
                if ($cleanPopulation > 0) {
                    Population::create([
                        'municipality' => $municipality,
                        'year' => (int) $year,
                        'population' => $cleanPopulation,
                    ]);
                    $imported++;
                }
            }
        }

        fclose($handle);

        $this->info("Successfully imported {$imported} population records");
        return 0;
    }
}
