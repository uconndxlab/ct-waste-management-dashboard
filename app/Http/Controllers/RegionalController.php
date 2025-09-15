<?php

namespace App\Http\Controllers;

use App\Services\RegionalDataService;
use App\Models\TownClassification;
use Illuminate\Http\Request;

class RegionalController extends Controller
{
    protected $regionalDataService;

    public function __construct(RegionalDataService $regionalDataService)
    {
        $this->regionalDataService = $regionalDataService;
    }

    /**
     * Display unified list of regions based on type
     * Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4, 7.1
     */
    public function listRegions($type)
    {
        // Validate region type
        $validTypes = ['county', 'planning-region', 'classification'];
        if (!in_array($type, $validTypes)) {
            abort(404, 'Invalid region type. Valid types are: ' . implode(', ', $validTypes));
        }

        try {
            // Get regional data based on type
            switch ($type) {
                case 'county':
                    $regions = $this->regionalDataService->getCountyTotals();
                    break;
                case 'planning-region':
                    $regions = $this->regionalDataService->getPlanningRegionTotals();
                    break;
                case 'classification':
                    $regions = $this->regionalDataService->getClassificationTotals();
                    break;
                default:
                    $regions = collect();
            }

            // Check if we have any regions
            if ($regions->isEmpty()) {
                $regionTypeLabel = str_replace('-', ' ', $type);
                session()->flash('warning', "No {$regionTypeLabel}s found in the system. Please ensure data has been imported.");
            }

            // Transform the data to include name field for consistency
            $regionsCollection = collect();
            foreach ($regions as $regionName => $data) {
                // Validate that we have a valid region name
                if (empty(trim($regionName))) {
                    continue; // Skip regions with empty names
                }
                
                $data->name = $regionName;
                $regionsCollection->push($data);
            }

            return view('regions.list', [
                'regions' => $regionsCollection,
                'regionType' => $type
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error loading regional data', [
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return with error message
            $regionTypeLabel = str_replace('-', ' ', $type);
            return back()->withErrors(['system' => "Unable to load {$regionTypeLabel} data. Please try again later."]);
        }
    }

    /**
     * Display list of all counties with aggregated data
     * Requirements: 1.1, 1.2, 1.3, 1.4
     */
    public function listCounties()
    {
        $countyTotals = $this->regionalDataService->getCountyTotals();
        $countyPopulations = $this->regionalDataService->getCountyPopulationTotals(2020); // Use 2020 as default year
        
        // Add population data to county totals
        foreach ($countyTotals as $county => $data) {
            $populationData = $countyPopulations->where('county', $county)->first();
            $data->total_population = $populationData ? $populationData->total_population : null;
            $data->municipalities_with_population_data = $populationData ? $populationData->municipalities_with_population_data : 0;
        }

        // Transform the data to include name field for consistency
        $regionsCollection = collect();
        foreach ($countyTotals as $regionName => $data) {
            $data->name = $regionName;
            $regionsCollection->push($data);
        }

        return view('regions.list', [
            'regions' => $regionsCollection,
            'regionType' => 'county'
        ]);
    }

    /**
     * Display list of all planning regions with aggregated data
     * Requirements: 2.1, 2.2, 2.3, 2.4
     */
    public function listPlanningRegions()
    {
        $planningRegionTotals = $this->regionalDataService->getPlanningRegionTotals();
        $planningRegionPopulations = $this->regionalDataService->getPlanningRegionPopulationTotals(2020); // Use 2020 as default year
        
        // Add population data to planning region totals
        foreach ($planningRegionTotals as $region => $data) {
            $populationData = $planningRegionPopulations->where('geographical_region', $region)->first();
            $data->total_population = $populationData ? $populationData->total_population : null;
            $data->municipalities_with_population_data = $populationData ? $populationData->municipalities_with_population_data : 0;
        }

        // Transform the data to include name field for consistency
        $regionsCollection = collect();
        foreach ($planningRegionTotals as $regionName => $data) {
            $data->name = $regionName;
            $regionsCollection->push($data);
        }

        return view('regions.list', [
            'regions' => $regionsCollection,
            'regionType' => 'planning-region'
        ]);
    }

    /**
     * Display list of all rural/urban classifications with aggregated data
     * Requirements: 3.1, 3.2, 3.3, 3.4
     */
    public function listClassifications()
    {
        $classificationTotals = $this->regionalDataService->getClassificationTotals();
        $classificationPopulations = $this->regionalDataService->getClassificationPopulationTotals(2020); // Use 2020 as default year
        
        // Add population data to classification totals
        foreach ($classificationTotals as $classification => $data) {
            $populationData = $classificationPopulations->where('region_type', $classification)->first();
            $data->total_population = $populationData ? $populationData->total_population : null;
            $data->municipalities_with_population_data = $populationData ? $populationData->municipalities_with_population_data : 0;
        }

        // Transform the data to include name field for consistency
        $regionsCollection = collect();
        foreach ($classificationTotals as $regionName => $data) {
            $data->name = $regionName;
            $regionsCollection->push($data);
        }

        return view('regions.list', [
            'regions' => $regionsCollection,
            'regionType' => 'classification'
        ]);
    }

    /**
     * Display detailed view of a specific region
     * Requirements: 1.5, 2.5, 3.5
     */
    public function viewRegion(Request $request, $regionType, $regionName)
    {
        // Validate region type
        $validRegionTypes = ['county', 'planning_region', 'classification'];
        if (!in_array($regionType, $validRegionTypes)) {
            abort(404, 'Invalid region type. Valid types are: ' . implode(', ', $validRegionTypes));
        }

        // Validate region name is not empty
        if (empty(trim($regionName))) {
            abort(404, 'Region name cannot be empty');
        }

        try {
            // Validate that the region exists before trying to get data
            $regionExists = $this->validateRegionsExist($regionType, [$regionName]);
            if (!$regionExists['valid']) {
                abort(404, $regionExists['message']);
            }

            // Get regional totals for the specific region
            $regionData = $this->regionalDataService->getRegionTotals($regionType, $regionName);
            
            if (!$regionData) {
                $regionTypeLabel = str_replace('_', ' ', $regionType);
                abort(404, "No data found for {$regionTypeLabel}: {$regionName}. This region may not have any municipalities with financial data.");
            }

            // Validate that the region has at least some municipalities
            if (($regionData->total_municipalities ?? 0) <= 0) {
                $regionTypeLabel = str_replace('_', ' ', $regionType);
                abort(404, "The {$regionTypeLabel} '{$regionName}' does not contain any municipalities.");
            }

            // Get population data for the region
            $populationData = $this->regionalDataService->getRegionPopulationTotals($regionType, $regionName, 2020);
            $latestPopulation = $populationData->first();
            
            if ($latestPopulation) {
                $regionData->total_population = $latestPopulation->total_population;
                $regionData->municipalities_with_population_data = $latestPopulation->municipalities_with_population_data;
                
                // Calculate per capita values
                $regionData = $this->regionalDataService->calculateRegionalPerCapita($regionData, $latestPopulation->total_population);
            } else {
                $regionData->total_population = null;
                $regionData->municipalities_with_population_data = 0;
                
                // Calculate per capita values with null population
                $regionData = $this->regionalDataService->calculateRegionalPerCapita($regionData, null);
            }

            // Get historical data for trend analysis
            $historicalData = $this->regionalDataService->getRegionalHistoricalData($regionType, $regionName);
            $availableYears = $this->regionalDataService->getRegionAvailableYears($regionType, $regionName);

            // Get municipalities in this region for detailed listing
            $municipalities = $this->getMunicipalitiesInRegion($regionType, $regionName);

            // Determine region type label and field name for the view
            $regionTypeLabels = [
                'county' => 'County',
                'planning-region' => 'Planning Region',
                'planning_region' => 'Planning Region', // Support both formats
                'classification' => 'Classification'
            ];

            return view('regions.view-region', [
                'regionData' => $regionData,
                'regionName' => $regionName,
                'regionType' => $regionType,
                'regionTypeLabel' => $regionTypeLabels[$regionType],
                'historicalData' => $historicalData,
                'availableYears' => $availableYears,
                'municipalities' => $municipalities
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error loading region details', [
                'regionType' => $regionType,
                'regionName' => $regionName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $regionTypeLabel = str_replace('_', ' ', $regionType);
            abort(500, "Unable to load details for {$regionTypeLabel}: {$regionName}. Please try again later.");
        }
    }

    /**
     * Handle regional comparison
     * Requirements: 4.2, 4.6, 5.2, 5.6, 6.2, 6.6
     */
    public function compareRegions(Request $request)
    {
        // Enhanced validation with custom messages
        $request->validate([
            'regions' => 'required|array|size:2',
            'region_type' => 'required|string|in:county,planning-region,classification'
        ], [
            'regions.required' => 'You must select regions for comparison.',
            'regions.array' => 'Invalid region selection format.',
            'regions.size' => 'You must select exactly two regions for comparison.',
            'region_type.required' => 'Region type is required.',
            'region_type.in' => 'Invalid region type selected.'
        ]);

        $regionType = $request->region_type;
        $regionNames = $request->regions;

        // Additional validation: ensure region names are not empty
        foreach ($regionNames as $regionName) {
            if (empty(trim($regionName))) {
                return back()->withErrors(['regions' => 'Invalid region names provided.'])->withInput();
            }
        }

        // Additional validation: ensure regions are not the same
        if ($regionNames[0] === $regionNames[1]) {
            return back()->withErrors(['regions' => 'You cannot compare a region with itself. Please select two different regions.'])->withInput();
        }

        // Validate that the selected regions exist in the database
        $validRegions = $this->validateRegionsExist($regionType, $regionNames);
        if (!$validRegions['valid']) {
            return back()->withErrors(['regions' => $validRegions['message']])->withInput();
        }

        // Get data for both regions with comprehensive financial data
        $regions = collect($regionNames)->map(function ($regionName) use ($regionType) {
            $regionData = $this->regionalDataService->getRegionTotals($regionType, $regionName);
            
            if (!$regionData) {
                return null;
            }

            // Get the most recent population data available (try multiple years)
            $populationData = $this->regionalDataService->getRegionPopulationTotals($regionType, $regionName);
            $latestPopulation = $populationData->sortByDesc('year')->first();
            
            if ($latestPopulation) {
                $regionData->total_population = $latestPopulation->total_population;
                $regionData->municipalities_with_population_data = $latestPopulation->municipalities_with_population_data;
                $regionData->population_year = $latestPopulation->year;
                
                // Calculate per capita values for all financial fields
                $regionData = $this->regionalDataService->calculateRegionalPerCapita($regionData, $latestPopulation->total_population);
            } else {
                $regionData->total_population = null;
                $regionData->municipalities_with_population_data = 0;
                $regionData->population_year = null;
                
                // Calculate per capita values with null population (will set all to null)
                $regionData = $this->regionalDataService->calculateRegionalPerCapita($regionData, null);
            }

            // Add region name for display
            $regionData->region_name = $regionName;
            
            return $regionData;
        })->filter(); // Remove any null results

        // Ensure we have exactly 2 regions with data
        if ($regions->count() !== 2) {
            $missingRegions = collect($regionNames)->diff($regions->pluck('region_name'));
            $missingRegionsList = $missingRegions->implode(', ');
            return back()->withErrors(['regions' => "Could not find data for the following regions: {$missingRegionsList}. Please verify the regions exist and have data."])->withInput();
        }

        // Use the comprehensive validation method from the service
        foreach ($regionNames as $regionName) {
            $validation = $this->regionalDataService->validateRegionForComparison($regionType, $regionName);
            if (!$validation['valid']) {
                return back()->withErrors(['regions' => $validation['message']])->withInput();
            }
        }

        // Get historical data for both regions
        $region1Name = $regionNames[0];
        $region2Name = $regionNames[1];
        
        $region1Historical = $this->regionalDataService->getRegionalHistoricalData($regionType, $region1Name);
        $region2Historical = $this->regionalDataService->getRegionalHistoricalData($regionType, $region2Name);
        
        // Get common years for trend analysis
        $region1Years = $region1Historical->pluck('year')->toArray();
        $region2Years = $region2Historical->pluck('year')->toArray();
        $commonYears = array_intersect($region1Years, $region2Years);
        sort($commonYears);

        // Prepare trend data for charts with per capita calculations
        $region1TrendData = $this->prepareTrendData($region1Historical, $regionType, $region1Name, $commonYears);
        $region2TrendData = $this->prepareTrendData($region2Historical, $regionType, $region2Name, $commonYears);

        // Determine region type label for the view
        $regionTypeLabels = [
            'county' => 'Counties',
            'planning-region' => 'Planning Regions',
            'planning_region' => 'Planning Regions', // Support both formats
            'classification' => 'Classifications'
        ];

        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return view('regions.compare-content', [
                'regions' => $regions,
                'regionType' => $regionType,
                'regionTypeLabel' => $regionTypeLabels[$regionType],
                'commonYears' => $commonYears,
                'region1Historical' => $region1Historical->whereIn('year', $commonYears),
                'region2Historical' => $region2Historical->whereIn('year', $commonYears),
                'region1TrendData' => $region1TrendData,
                'region2TrendData' => $region2TrendData
            ]);
        }

        return view('regions.compare', [
            'regions' => $regions,
            'regionType' => $regionType,
            'regionTypeLabel' => $regionTypeLabels[$regionType],
            'commonYears' => $commonYears,
            'region1Historical' => $region1Historical->whereIn('year', $commonYears),
            'region2Historical' => $region2Historical->whereIn('year', $commonYears),
            'region1TrendData' => $region1TrendData,
            'region2TrendData' => $region2TrendData
        ]);
    }

    /**
     * Get municipalities within a specific region
     */
    private function getMunicipalitiesInRegion($regionType, $regionName)
    {
        $fieldMap = [
            'county' => 'county',
            'planning-region' => 'geographical_region',
            'planning_region' => 'geographical_region', // Support both formats
            'classification' => 'region_type'
        ];

        if (!isset($fieldMap[$regionType])) {
            return collect();
        }

        return TownClassification::where($fieldMap[$regionType], $regionName)
            ->orderBy('municipality')
            ->pluck('municipality');
    }

    /**
     * Validate that the selected regions exist in the database
     */
    private function validateRegionsExist(string $regionType, array $regionNames): array
    {
        $fieldMap = [
            'county' => 'county',
            'planning-region' => 'geographical_region',
            'planning_region' => 'geographical_region', // Support both formats
            'classification' => 'region_type'
        ];

        if (!isset($fieldMap[$regionType])) {
            return [
                'valid' => false,
                'message' => 'Invalid region type provided.'
            ];
        }

        $field = $fieldMap[$regionType];
        
        // Get all existing regions of this type
        $existingRegions = TownClassification::whereNotNull($field)
            ->distinct()
            ->pluck($field)
            ->map(function ($name) {
                return trim($name);
            })
            ->filter()
            ->values();

        // Check if all selected regions exist
        $missingRegions = collect($regionNames)->diff($existingRegions);
        
        if ($missingRegions->count() > 0) {
            $missingList = $missingRegions->implode(', ');
            $regionTypeLabel = str_replace('_', ' ', $regionType);
            return [
                'valid' => false,
                'message' => "The following {$regionTypeLabel}(s) do not exist: {$missingList}. Please select valid regions from the list."
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Prepare trend data for charts with per capita calculations for all financial fields
     */
    private function prepareTrendData($historicalData, $regionType, $regionName, $commonYears)
    {
        // Initialize trend data for all financial fields
        $trendData = [
            'total_bulky_waste' => [],
            'total_recycling' => [],
            'total_tipping_fees' => [],
            'total_admin_costs' => [],
            'total_hazardous_waste' => [],
            'total_contractual_services' => [],
            'total_landfill_costs' => [],
            'total_total_sanitation_refuse' => [],
            'total_only_public_works' => [],
            'total_transfer_station_wages' => [],
            'total_hauling_fees' => [],
            'total_curbside_pickup_fees' => [],
            'total_waste_collection' => []
        ];

        foreach ($historicalData->whereIn('year', $commonYears) as $record) {
            // Get population for this specific year
            $populationData = $this->regionalDataService->getRegionPopulationTotals($regionType, $regionName, (int) $record->year);
            $population = $populationData->first();
            $populationCount = $population ? $population->total_population : 1; // Default to 1 to avoid division by zero

            // Calculate per capita for all financial metrics
            foreach (array_keys($trendData) as $field) {
                $value = $record->{$field} ?? 0;
                $trendData[$field][] = $populationCount > 0 ? round($value / $populationCount, 2) : 0;
            }
        }

        return $trendData;
    }
}