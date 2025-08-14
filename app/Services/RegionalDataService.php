<?php

namespace App\Services;

use App\Models\Municipality;
use App\Models\Population;
use App\Models\TownClassification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RegionalDataService
{
    /**
     * All financial fields that should be aggregated
     */
    private const FINANCIAL_FIELDS = [
        'bulky_waste',
        'recycling',
        'tipping_fees',
        'admin_costs',
        'hazardous_waste',
        'contractual_services',
        'landfill_costs',
        'total_sanitation_refuse',
        'only_public_works',
        'transfer_station_wages',
        'hauling_fees',
        'curbside_pickup_fees',
        'waste_collection',
    ];

    /**
     * Convert currency string to numeric value
     */
    private function currencyToNumeric($currencyString): ?float
    {
        if (empty($currencyString)) {
            return null;
        }
        
        $numeric = preg_replace('/[^\d.-]/', '', $currencyString);
        return is_numeric($numeric) ? (float)$numeric : null;
    }

    /**
     * Build the SELECT clause for financial field aggregation
     */
    private function buildFinancialSelectClause(string $regionField): string
    {
        $selectParts = [$regionField];
        
        foreach (self::FINANCIAL_FIELDS as $field) {
            $selectParts[] = "SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.{$field}, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_{$field}";
        }
        
        $selectParts[] = "COUNT(DISTINCT municipalities.name) as total_municipalities";
        $selectParts[] = "COUNT(DISTINCT CASE WHEN municipalities.total_sanitation_refuse IS NOT NULL THEN municipalities.name END) as municipalities_with_data";
        
        return implode(', ', $selectParts);
    }

    /**
     * Get aggregated financial data by county
     */
    public function getCountyTotals(): Collection
    {
        try {
            $selectClause = $this->buildFinancialSelectClause('town_classifications.county');
            
            $results = Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
                ->selectRaw($selectClause)
                ->whereNotNull('town_classifications.county')
                ->where('town_classifications.county', '!=', '')
                ->groupBy('town_classifications.county')
                ->get()
                ->keyBy('county');

            // Filter out any results with empty county names
            return $results->filter(function ($item, $key) {
                return !empty(trim($key));
            });

        } catch (\Exception $e) {
            \Log::error('Error getting county totals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get aggregated financial data by planning region
     */
    public function getPlanningRegionTotals(): Collection
    {
        try {
            $selectClause = $this->buildFinancialSelectClause('town_classifications.geographical_region');
            
            $results = Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
                ->selectRaw($selectClause)
                ->whereNotNull('town_classifications.geographical_region')
                ->where('town_classifications.geographical_region', '!=', '')
                ->groupBy('town_classifications.geographical_region')
                ->get()
                ->keyBy('geographical_region');

            // Filter out any results with empty region names
            return $results->filter(function ($item, $key) {
                return !empty(trim($key));
            });

        } catch (\Exception $e) {
            \Log::error('Error getting planning region totals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get aggregated financial data by classification type
     */
    public function getClassificationTotals(): Collection
    {
        try {
            $selectClause = $this->buildFinancialSelectClause('town_classifications.region_type');
            
            $results = Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
                ->selectRaw($selectClause)
                ->whereNotNull('town_classifications.region_type')
                ->where('town_classifications.region_type', '!=', '')
                ->groupBy('town_classifications.region_type')
                ->get()
                ->keyBy('region_type');

            // Filter out any results with empty classification names
            return $results->filter(function ($item, $key) {
                return !empty(trim($key));
            });

        } catch (\Exception $e) {
            \Log::error('Error getting classification totals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get aggregated financial data for a specific region by type and name
     */
    public function getRegionTotals(string $regionType, string $regionName): ?object
    {
        try {
            $fieldMap = [
                'county' => 'town_classifications.county',
                'planning-region' => 'town_classifications.geographical_region',
                'planning_region' => 'town_classifications.geographical_region', // Support both formats
                'classification' => 'town_classifications.region_type',
            ];

            if (!isset($fieldMap[$regionType])) {
                \Log::warning('Invalid region type provided', ['regionType' => $regionType]);
                return null;
            }

            // Validate region name is not empty
            if (empty(trim($regionName))) {
                \Log::warning('Empty region name provided', ['regionType' => $regionType, 'regionName' => $regionName]);
                return null;
            }

            $regionField = $fieldMap[$regionType];
            $selectClause = $this->buildFinancialSelectClause($regionField);
            
            $result = Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
                ->selectRaw($selectClause)
                ->where($regionField, $regionName)
                ->groupBy($regionField)
                ->first();

            // Validate that we got meaningful data
            if ($result && ($result->total_municipalities ?? 0) <= 0) {
                \Log::warning('Region found but contains no municipalities', [
                    'regionType' => $regionType, 
                    'regionName' => $regionName
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            \Log::error('Error getting region totals', [
                'regionType' => $regionType,
                'regionName' => $regionName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get population totals by county and year
     */
    public function getCountyPopulationTotals(?int $year = null): Collection
    {
        $query = Population::join('town_classifications', 'populations.municipality', '=', 'town_classifications.municipality')
            ->selectRaw('
                town_classifications.county,
                populations.year,
                SUM(populations.population) as total_population,
                COUNT(DISTINCT populations.municipality) as municipalities_with_population_data
            ')
            ->whereNotNull('town_classifications.county')
            ->groupBy('town_classifications.county', 'populations.year')
            ->orderBy('populations.year');

        if ($year) {
            $query->where('populations.year', $year);
        }

        return $query->get();
    }

    /**
     * Get population totals by planning region and year
     */
    public function getPlanningRegionPopulationTotals(?int $year = null): Collection
    {
        $query = Population::join('town_classifications', 'populations.municipality', '=', 'town_classifications.municipality')
            ->selectRaw('
                town_classifications.geographical_region,
                populations.year,
                SUM(populations.population) as total_population,
                COUNT(DISTINCT populations.municipality) as municipalities_with_population_data
            ')
            ->whereNotNull('town_classifications.geographical_region')
            ->groupBy('town_classifications.geographical_region', 'populations.year')
            ->orderBy('populations.year');

        if ($year) {
            $query->where('populations.year', $year);
        }

        return $query->get();
    }

    /**
     * Get population totals by classification and year
     */
    public function getClassificationPopulationTotals(?int $year = null): Collection
    {
        $query = Population::join('town_classifications', 'populations.municipality', '=', 'town_classifications.municipality')
            ->selectRaw('
                town_classifications.region_type,
                populations.year,
                SUM(populations.population) as total_population,
                COUNT(DISTINCT populations.municipality) as municipalities_with_population_data
            ')
            ->whereNotNull('town_classifications.region_type')
            ->groupBy('town_classifications.region_type', 'populations.year')
            ->orderBy('populations.year');

        if ($year) {
            $query->where('populations.year', $year);
        }

        return $query->get();
    }

    /**
     * Get population totals for a specific region by type, name and year
     */
    public function getRegionPopulationTotals(string $regionType, string $regionName, ?int $year = null): Collection
    {
        $fieldMap = [
            'county' => 'town_classifications.county',
            'planning-region' => 'town_classifications.geographical_region',
            'planning_region' => 'town_classifications.geographical_region', // Support both formats
            'classification' => 'town_classifications.region_type',
        ];

        if (!isset($fieldMap[$regionType])) {
            return collect();
        }

        $regionField = $fieldMap[$regionType];
        
        $query = Population::join('town_classifications', 'populations.municipality', '=', 'town_classifications.municipality')
            ->selectRaw("
                {$regionField} as region_name,
                populations.year,
                SUM(populations.population) as total_population,
                COUNT(DISTINCT populations.municipality) as municipalities_with_population_data
            ")
            ->where($regionField, $regionName)
            ->groupBy($regionField, 'populations.year')
            ->orderBy('populations.year');

        if ($year) {
            $query->where('populations.year', $year);
        }

        return $query->get();
    }

    /**
     * Get historical financial data for a specific region across multiple years
     */
    public function getRegionalHistoricalData(string $regionType, string $regionName): Collection
    {
        $fieldMap = [
            'county' => 'town_classifications.county',
            'planning-region' => 'town_classifications.geographical_region',
            'planning_region' => 'town_classifications.geographical_region', // Support both formats
            'classification' => 'town_classifications.region_type',
        ];

        if (!isset($fieldMap[$regionType])) {
            return collect();
        }

        $regionField = $fieldMap[$regionType];
        
        // Build select clause with year
        $selectParts = [
            $regionField . ' as region_name',
            'municipalities.year'
        ];
        
        foreach (self::FINANCIAL_FIELDS as $field) {
            $selectParts[] = "SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.{$field}, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_{$field}";
        }
        
        $selectParts[] = "COUNT(DISTINCT municipalities.name) as total_municipalities";
        $selectParts[] = "COUNT(DISTINCT CASE WHEN municipalities.total_sanitation_refuse IS NOT NULL THEN municipalities.name END) as municipalities_with_data";
        
        $selectClause = implode(', ', $selectParts);
        
        return Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->selectRaw($selectClause)
            ->where($regionField, $regionName)
            ->whereNotNull('municipalities.year')
            ->groupBy($regionField, 'municipalities.year')
            ->orderBy('municipalities.year')
            ->get();
    }

    /**
     * Calculate per capita values for regional data
     */
    public function calculateRegionalPerCapita(object $regionalData, ?int $totalPopulation): object
    {
        if (!$totalPopulation || $totalPopulation <= 0) {
            // Add per capita fields as null when no population data
            foreach (self::FINANCIAL_FIELDS as $field) {
                $regionalData->{"total_{$field}_per_capita"} = null;
            }
            return $regionalData;
        }

        // Calculate per capita for each financial field
        foreach (self::FINANCIAL_FIELDS as $field) {
            $totalValue = $regionalData->{"total_{$field}"} ?? 0;
            $regionalData->{"total_{$field}_per_capita"} = $totalValue > 0 ? round($totalValue / $totalPopulation, 2) : 0;
        }

        return $regionalData;
    }

    /**
     * Validate that a region has sufficient data for comparison
     */
    public function validateRegionForComparison(string $regionType, string $regionName): array
    {
        try {
            $regionData = $this->getRegionTotals($regionType, $regionName);
            
            if (!$regionData) {
                return [
                    'valid' => false,
                    'message' => "Region '{$regionName}' not found or has no data."
                ];
            }

            if (($regionData->total_municipalities ?? 0) <= 0) {
                return [
                    'valid' => false,
                    'message' => "Region '{$regionName}' contains no municipalities."
                ];
            }

            if (($regionData->municipalities_with_data ?? 0) <= 0) {
                return [
                    'valid' => false,
                    'message' => "Region '{$regionName}' has no financial data available."
                ];
            }

            return ['valid' => true, 'message' => ''];

        } catch (\Exception $e) {
            \Log::error('Error validating region for comparison', [
                'regionType' => $regionType,
                'regionName' => $regionName,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'message' => "Unable to validate region '{$regionName}'. Please try again."
            ];
        }
    }

    /**
     * Extract population year from fiscal year string
     */
    private function extractPopulationYear($fiscalYear): ?int
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
        
        // Handle fiscal year ranges: "2019-2020" -> use the later year (2020)
        if (strpos($fiscalYear, '-') !== false) {
            $parts = explode('-', $fiscalYear);
            if (count($parts) >= 2 && is_numeric($parts[1])) {
                return (int) $parts[1];
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
     * Get all available years for a specific region
     */
    public function getRegionAvailableYears(string $regionType, string $regionName): Collection
    {
        $fieldMap = [
            'county' => 'town_classifications.county',
            'planning-region' => 'town_classifications.geographical_region',
            'planning_region' => 'town_classifications.geographical_region', // Support both formats
            'classification' => 'town_classifications.region_type',
        ];

        if (!isset($fieldMap[$regionType])) {
            return collect();
        }

        $regionField = $fieldMap[$regionType];
        
        return Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->select('municipalities.year')
            ->where($regionField, $regionName)
            ->whereNotNull('municipalities.year')
            ->distinct()
            ->orderBy('municipalities.year')
            ->pluck('year');
    }
}