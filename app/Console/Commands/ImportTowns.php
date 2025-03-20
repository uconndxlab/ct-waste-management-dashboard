<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Municipality;
use League\Csv\Reader;

class ImportTowns extends Command
{
    protected $signature = 'import:towns {file}';
    protected $description = 'Import towns from a CSV file';

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
            Municipality::updateOrCreate(
                ['name' => $record['Municipality'], 'year' => $record['Year']],
                [
                    'bulky_waste' => $record['Bulky Waste'] ?? null,
                    'recycling' => $record['Recycling'] ?? null,
                    'tipping_fees' => $record['Tipping Fees'] ?? null,
                    'admin_costs' => $record['Admin Costs'] ?? null,
                    'hazardous_waste' => $record['Hazardous Waste'] ?? null,
                    'contractual_services' => $record['Contractual Services'] ?? null,
                    'landfill_costs' => $record['Landfill Costs'] ?? null,
                    'total_sanitation_refuse' => $record['Total Sanitation/refuse'] ?? null,
                    'only_public_works' => $record['Only Public Works'] ?? null,
                    'transfer_station_wages' => $record['Transfer station/Recycling Center wages'] ?? null,
                    'hauling_fees' => $record['Hauling fees'] ?? null,
                    'curbside_pickup_fees' => $record['Curbside Pickup fees (town)'] ?? null,
                    'waste_collection' => $record['Waste Collection'] ?? null,
                    'notes' => $record['notes/ other broken down variables'] ?? null, // Ensure this matches CSV
                ]
            );
        }
        

    }
}
