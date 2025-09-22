<?php

namespace App\Services;

use App\Models\Municipality;
use App\Models\Population;
use App\Models\TownClassification;
use Illuminate\Support\Collection;

class MunicipalityService
{
    /**
     * Get municipalities with their latest financial data
     */
    public function getMunicipalitiesWithLatestData(): Collection
    {
        return Municipality::select('name', 'href')
            ->selectRaw('MAX(tipping_fees) as tipping_fees')
            ->selectRaw('MAX(total_sanitation_refuse) as total_sanitation_refuse')
            ->selectRaw('MAX(bulky_waste) as bulky_waste')
            ->selectRaw('MAX(curbside_pickup_fees) as curbside_pickup_fees')
            ->selectRaw('MAX(hazardous_waste) as hazardous_waste')
            ->selectRaw('MAX(recycling) as recycling')
            ->selectRaw('MAX(transfer_station_wages) as transfer_station_wages')
            ->selectRaw('MAX(year) as latest_year')
            ->groupBy('name', 'href')
            ->get();
    }

    /**
     * Get county totals for financial data
     */
    public function getCountyTotals(): Collection
    {
        return Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->selectRaw("
                town_classifications.county, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.tipping_fees, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_tipping_fees, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.recycling, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_recycling_fees,
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.transfer_station_wages, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_transfer_station_costs,
                COUNT(DISTINCT municipalities.name) as total_municipalities, 
                COUNT(DISTINCT CASE WHEN municipalities.tipping_fees IS NOT NULL THEN municipalities.name END) as municipalities_with_data
            ")
            ->whereNotNull('town_classifications.county') 
            ->groupBy('town_classifications.county')
            ->get()
            ->keyBy('county');
    }

    /**
     * Get region totals for financial data
     */
    public function getRegionTotals(): Collection
    {
        return Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->selectRaw("
                town_classifications.geographical_region, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.tipping_fees, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_tipping_fees, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.total_sanitation_refuse, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_sanitation_refuse,
                COUNT(DISTINCT municipalities.name) as total_municipalities, 
                COUNT(DISTINCT CASE WHEN municipalities.tipping_fees IS NOT NULL THEN municipalities.name END) as municipalities_with_data
            ")
            ->whereNotNull('town_classifications.geographical_region') 
            ->groupBy('town_classifications.geographical_region')
            ->get()
            ->keyBy('geographical_region');
    }

    /**
     * Get type totals for financial data
     */
    public function getTypeTotals(): Collection
    {
        return Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->selectRaw("
                town_classifications.region_type, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.tipping_fees, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_tipping_fees, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.total_sanitation_refuse, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_sanitation_refuse,
                COUNT(DISTINCT municipalities.name) as total_municipalities, 
                COUNT(DISTINCT CASE WHEN municipalities.tipping_fees IS NOT NULL THEN municipalities.name END) as municipalities_with_data
            ")
            ->whereNotNull('town_classifications.region_type') 
            ->groupBy('town_classifications.region_type')
            ->get()
            ->keyBy('region_type');
    }

    /**
     * Find population for a municipality by name (case-insensitive)
     */
    public function findPopulationByMunicipality(string $municipalityName, int $year): ?Population
    {
        return Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipalityName])
            ->where('year', $year)
            ->first();
    }

    /**
     * Find population with fallback to 2020 if year is before 2020
     */
    public function findPopulationWithFallback(string $municipalityName, int $year): ?Population
    {
        $population = $this->findPopulationByMunicipality($municipalityName, $year);
        
        if (!$population && $year < 2020) {
            $population = $this->findPopulationByMunicipality($municipalityName, 2020);
        }
        
        return $population;
    }

    /**
     * Get latest population for a municipality
     */
    public function getLatestPopulation(string $municipalityName): ?Population
    {
        return Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipalityName])
            ->orderBy('year', 'desc')
            ->first();
    }

    /**
     * Get populations for multiple years
     */
    public function getPopulationsForYears(string $municipalityName, array $years): Collection
    {
        return Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipalityName])
            ->whereIn('year', $years)
            ->orderBy('year')
            ->get()
            ->keyBy('year');
    }

    /**
     * Convert currency string to numeric value
     */
    public function currencyToNumeric(?string $currencyString): ?float
    {
        if (empty($currencyString)) {
            return null;
        }
        
        $numeric = preg_replace('/[^\d.-]/', '', $currencyString);
        return is_numeric($numeric) ? (float)$numeric : null;
    }

    /**
     * Extract population year from fiscal year string
     */
    public function extractPopulationYear($fiscalYear): ?int
    {
        // Handle empty or invalid data
        if (empty($fiscalYear) || $fiscalYear === 'No Budget Info') {
            return null;
        }
        
        // Clean the input - remove extra spaces
        $fiscalYear = trim($fiscalYear);
        
        // Handle FY formats: "FY 2019", "FY2019" -> 2019
        if (preg_match('/FY\s*(\d{4})/', $fiscalYear, $matches)) {
            return (int) $matches[1];
        }
        
        // Handle fiscal year ranges: use the FIRST/EARLIER year
        if (strpos($fiscalYear, '-') !== false) {
            $parts = explode('-', $fiscalYear);
            if (count($parts) >= 2) {
                $firstPart = trim($parts[0]);
                $secondPart = trim($parts[1]);
                
                // Handle full year ranges like "2022-2023" -> 2022
                if (is_numeric($firstPart) && strlen($firstPart) === 4) {
                    return (int) $firstPart;
                }
                
                // Handle abbreviated ranges like "2021-22" -> 2021
                if (is_numeric($firstPart) && strlen($firstPart) === 4 && is_numeric($secondPart) && strlen($secondPart) === 2) {
                    return (int) $firstPart;
                }
            }
        }
        
        // Handle regular years: "2019" -> 2019
        if (is_numeric($fiscalYear)) {
            return (int) $fiscalYear;
        }
        
        // If we can't parse it, return null
        return null;
    }

    /**
     * Get alphabetical letters used by municipalities
     */
    public function getMunicipalityLetters(): Collection
    {
        return Municipality::selectRaw('SUBSTR(name, 1, 1) as letter')
            ->groupBy('letter')
            ->orderBy('letter')
            ->pluck('letter');
    }
}