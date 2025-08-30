<?php

namespace App\Http\Controllers;

use App\Models\OverallTownInfo;
use App\Models\MunicipalityFinancialData;
use App\Models\Municipality;
use App\Models\Population;
use App\Models\TownClassification;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{

    public function allMunicipalities(Request $request)
    {
        $letters = Municipality::selectRaw('SUBSTR(name, 1, 1) as letter')
            ->groupBy('letter')
            ->orderBy('letter')
            ->pluck('letter');
    
        $selectedLetter = $request->input('letter', null);
        $search = $request->input('search', '');

        // Get 'Filter by Index' parameters
        $regionType = $request->input('region_type');
        $geographicalRegion = $request->input('geographical_region');
        $county = $request->input('county');
    
        $query = Municipality::query() // Eloquent builder
            ->select('municipalities.name')
            ->leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality');
    
        if ($selectedLetter) {
            $query->where('municipalities.name', 'like', $selectedLetter . '%');
        }
    
        if ($search) {
            $query->where('municipalities.name', 'like', '%' . $search . '%');
        }

        if ($regionType) {
            $query->where('town_classifications.region_type', $regionType);
        }

        if ($geographicalRegion) {
            $query->where('town_classifications.geographical_region', $geographicalRegion);
        }
        if ($county) {
            $query->where('town_classifications.county', $county);
        }

        $query->groupBy('municipalities.name')
          ->orderBy('municipalities.name');

        $municipalities = $query->get();
        
        // Get unique values for dropdowns
        $regionTypes = TownClassification::distinct()->orderBy('region_type')->pluck('region_type');
        $geographicalRegions = TownClassification::distinct()->orderBy('geographical_region')->pluck('geographical_region');
        $counties = TownClassification::distinct()->orderBy('county')->pluck('county');
    
        return view('municipalities.view-all', compact(
            'municipalities', 
            'letters', 
            'selectedLetter', 
            'search',
            'regionTypes',
            'geographicalRegions',
            'counties',
            'regionType',
            'geographicalRegion',
            'county'
        ));
    }

    public function showHome()
    {
        // Get unique municipalities with their latest/maximum refuse value and year
        $municipalities = Municipality::select('name', 'href')
            ->selectRaw('MAX(total_sanitation_refuse) as total_sanitation_refuse')
            ->selectRaw('MAX(year) as latest_year')
            ->groupBy('name', 'href')
            ->get();
            
        // Process and standardize the year values
        foreach ($municipalities as $municipality) {
            if ($municipality->latest_year) {
                $standardizedYear = $this->extractPopulationYear($municipality->latest_year);
                $municipality->latest_year = $standardizedYear;
            }
        }
            
        $townClassifications = TownClassification::all()->keyBy('municipality');

        // Dealing with money to num

        $countyTotals = Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->selectRaw("
                town_classifications.county, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.total_sanitation_refuse, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_refuse, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.admin_costs, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_admin,
                COUNT(DISTINCT municipalities.name) as total_municipalities, 
                COUNT(DISTINCT CASE WHEN municipalities.total_sanitation_refuse IS NOT NULL THEN municipalities.name END) as municipalities_with_data
            ")
            ->whereNotNull('town_classifications.county') 
            ->groupBy('town_classifications.county')
            ->get()
            ->keyBy('county');
        
        $regionTotals = Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->selectRaw("
                town_classifications.geographical_region, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.total_sanitation_refuse, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_refuse, 
                COUNT(DISTINCT municipalities.name) as total_municipalities, 
                COUNT(DISTINCT CASE WHEN municipalities.total_sanitation_refuse IS NOT NULL THEN municipalities.name END) as municipalities_with_data
            ")
            ->whereNotNull('town_classifications.geographical_region') 
            ->groupBy('town_classifications.geographical_region')
            ->get()
            ->keyBy('geographical_region');
        
        $typeTotals = Municipality::leftJoin('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->selectRaw("
                town_classifications.region_type, 
                SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.total_sanitation_refuse, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_refuse, 
                COUNT(DISTINCT municipalities.name) as total_municipalities, 
                COUNT(DISTINCT CASE WHEN municipalities.total_sanitation_refuse IS NOT NULL THEN municipalities.name END) as municipalities_with_data
            ")
            ->whereNotNull('town_classifications.region_type') 
            ->groupBy('town_classifications.region_type')
            ->get()
            ->keyBy('region_type');

        // Keep the test query for debugging
        $test = Municipality::join('town_classifications', 'municipalities.name', '=', 'town_classifications.municipality')
            ->select('municipalities.name', 'municipalities.total_sanitation_refuse', 'town_classifications.county', 'town_classifications.geographical_region', 'town_classifications.region_type')
            ->limit(10)
            ->get();



        return view('welcome', compact('municipalities', 'townClassifications', 'countyTotals', 'regionTotals', 'typeTotals', 'test'));
    }


    private function currencyToNumeric($currencyString)
    {
        if (empty($currencyString)) {
            return null;
        }
        
        $numeric = preg_replace('/[^\d.-]/', '', $currencyString);
        return is_numeric($numeric) ? (float)$numeric : null;
    }

    private function extractPopulationYear($fiscalYear)
    {
        // Handle various year formats and return the most appropriate population year
        
        // Handle empty or invalid data
        if (empty($fiscalYear) || $fiscalYear === 'No Budget Info') {
            return null; // Return null for invalid data
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
    

    public function viewMunicipality($name)
    {
        $municipality = Municipality::where('name', $name)->firstOrFail();

        $reports = Municipality::where('name', $name)->orderBy('year')->get();

        $townInfo = OverallTownInfo::where('municipality', $name)->first();
        
        $townClassification = TownClassification::where('municipality', $name)->first();
        
        $financials = MunicipalityFinancialData::where('municipality', $name)->get();
        
        $financialData = MunicipalityFinancialData::where('municipality', $name)->firstOrFail();

        // Check if we have population data for any of the reports
        $hasAnyPopulationData = false;
        $population = null;
        
        // Check each report to see if we have population data
        foreach($reports as $report) {
            $reportPopulationYear = $this->extractPopulationYear($report->year);
            if ($reportPopulationYear) { // Only query if we have a valid year
                $reportPopulation = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$name])
                    ->where('year', $reportPopulationYear)
                    ->first();
                
                // If no exact match and year is before 2020, use 2020 as closest approximation
                if (!$reportPopulation && $reportPopulationYear < 2020) {
                    $reportPopulation = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$name])
                        ->where('year', 2020)
                        ->first();
                }
                
                if ($reportPopulation) {
                    $hasAnyPopulationData = true;
                    if (!$population) {
                        $population = $reportPopulation->population; // Use first found population for header display
                    }
                }
            }
        }

        if ($hasAnyPopulationData) {
            foreach($reports as $report) {
                // Get the correct population for each report's year
                $reportPopulationYear = $this->extractPopulationYear($report->year);
                $reportPopulation = null;
                $reportPopulationCount = null;
                
                $actualPopulationYear = null;
                if ($reportPopulationYear) { // Only query if we have a valid year
                    $reportPopulation = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$name])
                        ->where('year', $reportPopulationYear)
                        ->first();
                    
                    if ($reportPopulation) {
                        $actualPopulationYear = $reportPopulationYear;
                    } else if ($reportPopulationYear < 2020) {
                        // If no exact match and year is before 2020, use 2020 as closest approximation
                        $reportPopulation = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$name])
                            ->where('year', 2020)
                            ->first();
                        $actualPopulationYear = $reportPopulation ? '2020*' : null;
                    }
                    
                    $reportPopulationCount = $reportPopulation ? $reportPopulation->population : null;
                }

                $recycling = $this->currencyToNumeric($report->recycling);
                $tippingFees = $this->currencyToNumeric($report->tipping_fees);
                $transferStationWages = $this->currencyToNumeric($report->transfer_station_wages);
                $totalSanitation = $this->currencyToNumeric($report->total_sanitation_refuse);

                // Calculate per capita for all financial fields
                $bulkyWaste = $this->currencyToNumeric($report->bulky_waste);
                $adminCosts = $this->currencyToNumeric($report->admin_costs);
                $hazardousWaste = $this->currencyToNumeric($report->hazardous_waste);
                $contractualServices = $this->currencyToNumeric($report->contractual_services);
                $landfillCosts = $this->currencyToNumeric($report->landfill_costs);
                $onlyPublicWorks = $this->currencyToNumeric($report->only_public_works);
                $haulingFees = $this->currencyToNumeric($report->hauling_fees);
                $curbsidePickupFees = $this->currencyToNumeric($report->curbside_pickup_fees);
                $wasteCollection = $this->currencyToNumeric($report->waste_collection);

                // Check if we have population data for this specific report
                $report->has_population_data = $reportPopulation ? true : false;
                $report->report_population = $reportPopulation ? $reportPopulation->population : null;
                $report->population_year_used = $actualPopulationYear;
                
                if ($reportPopulation && $reportPopulationCount > 0) {
                    $report->bulky_waste_per_capita = $bulkyWaste ? number_format($bulkyWaste / $reportPopulationCount, 2) : null;
                    $report->recycling_per_capita = $recycling ? number_format($recycling / $reportPopulationCount, 2) : null;
                    $report->tipping_fees_per_capita = $tippingFees ? number_format($tippingFees / $reportPopulationCount, 2) : null;
                    $report->admin_costs_per_capita = $adminCosts ? number_format($adminCosts / $reportPopulationCount, 2) : null;
                    $report->hazardous_waste_per_capita = $hazardousWaste ? number_format($hazardousWaste / $reportPopulationCount, 2) : null;
                    $report->contractual_services_per_capita = $contractualServices ? number_format($contractualServices / $reportPopulationCount, 2) : null;
                    $report->landfill_costs_per_capita = $landfillCosts ? number_format($landfillCosts / $reportPopulationCount, 2) : null;
                    $report->total_sanitation_refuse_per_capita = $totalSanitation ? number_format($totalSanitation / $reportPopulationCount, 2) : null;
                    $report->only_public_works_per_capita = $onlyPublicWorks ? number_format($onlyPublicWorks / $reportPopulationCount, 2) : null;
                    $report->transfer_station_wages_per_capita = $transferStationWages ? number_format($transferStationWages / $reportPopulationCount, 2) : null;
                    $report->hauling_fees_per_capita = $haulingFees ? number_format($haulingFees / $reportPopulationCount, 2) : null;
                    $report->curbside_pickup_fees_per_capita = $curbsidePickupFees ? number_format($curbsidePickupFees / $reportPopulationCount, 2) : null;
                    $report->waste_collection_per_capita = $wasteCollection ? number_format($wasteCollection / $reportPopulationCount, 2) : null;
                } else {
                    // Set all per capita values to null when no population data
                    $report->bulky_waste_per_capita = null;
                    $report->recycling_per_capita = null;
                    $report->tipping_fees_per_capita = null;
                    $report->admin_costs_per_capita = null;
                    $report->hazardous_waste_per_capita = null;
                    $report->contractual_services_per_capita = null;
                    $report->landfill_costs_per_capita = null;
                    $report->total_sanitation_refuse_per_capita = null;
                    $report->only_public_works_per_capita = null;
                    $report->transfer_station_wages_per_capita = null;
                    $report->hauling_fees_per_capita = null;
                    $report->curbside_pickup_fees_per_capita = null;
                    $report->waste_collection_per_capita = null;
                }
            }
        }

        return view('municipalities.view-municipality', compact(
            'name', 'reports', 'townInfo', 'financials', 'financialData',
             'townClassification', 'municipality', 'population', 'hasAnyPopulationData'));
    }

    public function viewReport($id)
    {
        $municipality = Municipality::findOrFail($id);
        return view('municipalities.reports.view-report', compact('municipality'));
    }

    public function editReport($id)
    {
        $municipality = Municipality::findOrFail($id);
        
        return view('municipalities.reports.edit-report', compact('municipality'));
    }

    public function updateReport(Request $request, $id)
    {
        $municipality = Municipality::findOrFail($id);
        
        $validatedData = $request->validate([
            'bulky_waste' => 'nullable|string',
            'recycling' => 'nullable|string',
            'tipping_fees' => 'nullable|string',
            'admin_costs' => 'nullable|string',
            'hazardous_waste' => 'nullable|string',
            'contractual_services' => 'nullable|string',
            'landfill_costs' => 'nullable|string',
            'total_sanitation_refuse' => 'nullable|string',
            'only_public_works' => 'nullable|string',
            'transfer_station_wages' => 'nullable|string',
            'hauling_fees' => 'nullable|string',
            'curbside_pickup_fees' => 'nullable|string',
            'waste_collection' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $municipality->update($validatedData);
        
        return redirect()->route('municipalities.report', ['id' => $municipality->id])
        ->with('success', 'Report updated successfully');
    }

    public function editContacts($name)
    {
        $townInfo = OverallTownInfo::where('municipality', $name)->firstOrFail();
        return view('municipalities.edit-municipality', compact('townInfo', 'name'));
    }
    
    public function updateContacts(Request $request, $name)
    {
        $townInfo = OverallTownInfo::where('municipality', $name)->firstOrFail();
    
        $validatedData = $request->validate([
            'department' => 'nullable|string',
            'contact_1' => 'nullable|string',
            'title_1' => 'nullable|string',
            'phone_1' => 'nullable|string',
            'email_1' => 'nullable|email',
            'contact_2' => 'nullable|string',
            'title_2' => 'nullable|string',
            'phone_2' => 'nullable|string',
            'email_2' => 'nullable|email',
            'notes' => 'nullable|string',
            'other_useful_notes' => 'nullable|string',
        ]);
    
        $townInfo->update($validatedData);
    
        return redirect()->route('municipalities.view', ['name' => $name])
                         ->with('success', 'Town Contact Information Updated Successfully.');
    }
    
    public function createReport($name)
    {
        return view('municipalities.reports.create-report', compact('name'));
    }
    
    public function storeReport(Request $request, $name)
    {
        $validatedData = $request->validate([
            'year' => 'required|integer',
            'bulky_waste' => 'nullable|string',
            'recycling' => 'nullable|string',
            'tipping_fees' => 'nullable|string',
            'admin_costs' => 'nullable|string',
            'hazardous_waste' => 'nullable|string',
            'contractual_services' => 'nullable|string',
            'landfill_costs' => 'nullable|string',
            'total_sanitation_refuse' => 'nullable|string',
            'only_public_works' => 'nullable|string',
            'transfer_station_wages' => 'nullable|string',
            'hauling_fees' => 'nullable|string',
            'curbside_pickup_fees' => 'nullable|string',
            'waste_collection' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
    
        $municipality = Municipality::create(array_merge($validatedData, ['name' => $name]));
    
        return redirect()->route('municipalities.view', ['name' => $name])
            ->with('success', 'New Report Added Successfully.');
    }

    public function deleteReport($name, $reportId)
    {
        $report = Municipality::where('name', $name)->where('id', $reportId)->firstOrFail();
        $report->delete();
    
        return redirect()->route('municipalities.view', ['name' => $name])->with('success', 'Report Deleted Successfully.');
    }

    public function compareMunicipalities(Request $request)
    {
        $request->validate([
            'municipalities' => 'required|array|size:2',
        ], [
            'municipalities.size' => 'You must select exactly two municipalities for comparison.',
        ]);

        // Get the most recent record for each municipality
        $municipalities = collect($request->municipalities)->map(function ($name) {
            $municipality = Municipality::where('name', $name)
                ->orderBy('year', 'desc')
                ->first();
                
            if ($municipality) {
                // Get latest population for per capita calculations
                $latestPopulation = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$name])
                    ->orderBy('year', 'desc')
                    ->first();
                    
                $municipality->latest_population = $latestPopulation ? $latestPopulation->population : null;
                
                // Calculate per capita values using the correct population year
                $populationYear = $this->extractPopulationYear($municipality->year);
                $correctPopulation = null;
                
                if ($populationYear) { // Only query if we have a valid year
                    $correctPopulation = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$name])
                        ->where('year', $populationYear)
                        ->first();
                    
                    // If no exact match and year is before 2020, use 2020 as closest approximation
                    if (!$correctPopulation && $populationYear < 2020) {
                        $correctPopulation = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$name])
                            ->where('year', 2020)
                            ->first();
                    }
                }
                
                if ($correctPopulation && $correctPopulation->population > 0) {
                    $municipality->latest_population = $correctPopulation->population;
                    $municipality->recycling_per_capita = $this->currencyToNumeric($municipality->recycling) / $correctPopulation->population;
                    $municipality->tipping_fees_per_capita = $this->currencyToNumeric($municipality->tipping_fees) / $correctPopulation->population;
                    $municipality->transfer_station_wages_per_capita = $this->currencyToNumeric($municipality->transfer_station_wages) / $correctPopulation->population;
                    $municipality->total_sanitation_refuse_per_capita = $this->currencyToNumeric($municipality->total_sanitation_refuse) / $correctPopulation->population;
                }
            }
            
            return $municipality;
        })->filter(); // Remove any null results

        // Ensure we have exactly 2 municipalities
        if ($municipalities->count() !== 2) {
            return back()->withErrors(['municipalities' => 'Could not find data for the selected municipalities.']);
        }

        // Get year-over-year data for both municipalities
        $municipality1Name = $municipalities[0]->name;
        $municipality2Name = $municipalities[1]->name;
        
        // Get all years both municipalities have data for and standardize them
        $municipality1RawYears = Municipality::where('name', $municipality1Name)
            ->pluck('year')
            ->toArray();
        $municipality2RawYears = Municipality::where('name', $municipality2Name)
            ->pluck('year')
            ->toArray();
            
        // Standardize years and create mapping from standardized to raw years
        $municipality1YearMap = [];
        $municipality1StandardizedYears = [];
        foreach ($municipality1RawYears as $rawYear) {
            $standardizedYear = $this->extractPopulationYear($rawYear);
            if ($standardizedYear) {
                $municipality1YearMap[$standardizedYear] = $rawYear;
                $municipality1StandardizedYears[] = $standardizedYear;
            }
        }
        
        $municipality2YearMap = [];
        $municipality2StandardizedYears = [];
        foreach ($municipality2RawYears as $rawYear) {
            $standardizedYear = $this->extractPopulationYear($rawYear);
            if ($standardizedYear) {
                $municipality2YearMap[$standardizedYear] = $rawYear;
                $municipality2StandardizedYears[] = $standardizedYear;
            }
        }
        
        // Find common standardized years
        $commonYears = array_intersect($municipality1StandardizedYears, $municipality2StandardizedYears);
        sort($commonYears);
        
        // Get historical data for common years using raw year values for database queries
        $municipality1RawYearsForQuery = array_map(function($year) use ($municipality1YearMap) {
            return $municipality1YearMap[$year];
        }, $commonYears);
        $municipality2RawYearsForQuery = array_map(function($year) use ($municipality2YearMap) {
            return $municipality2YearMap[$year];
        }, $commonYears);
        
        $municipality1Historical = Municipality::where('name', $municipality1Name)
            ->whereIn('year', $municipality1RawYearsForQuery)
            ->orderBy('year')
            ->get();
        $municipality2Historical = Municipality::where('name', $municipality2Name)
            ->whereIn('year', $municipality2RawYearsForQuery)
            ->orderBy('year')
            ->get();
            
        // Standardize years in historical data for consistent processing
        foreach ($municipality1Historical as $record) {
            $record->standardized_year = $this->extractPopulationYear($record->year);
        }
        foreach ($municipality2Historical as $record) {
            $record->standardized_year = $this->extractPopulationYear($record->year);
        }
        
        // Sort by standardized year
        $municipality1Historical = $municipality1Historical->sortBy('standardized_year');
        $municipality2Historical = $municipality2Historical->sortBy('standardized_year');
            
        // Get population data for per capita historical calculations using standardized years
        $municipality1Populations = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipality1Name])
            ->whereIn('year', $commonYears)
            ->orderBy('year')
            ->get()
            ->keyBy('year');
        $municipality2Populations = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipality2Name])
            ->whereIn('year', $commonYears)
            ->orderBy('year')
            ->get()
            ->keyBy('year');

        // Prepare trend data for charts - calculate all key metrics
        $municipality1TrendData = [
            'recycling' => [],
            'tipping_fees' => [],
            'transfer_station_wages' => [],
            'total_sanitation_refuse' => []
        ];
        $municipality2TrendData = [
            'recycling' => [],
            'tipping_fees' => [],
            'transfer_station_wages' => [],
            'total_sanitation_refuse' => []
        ];
        
        foreach($municipality1Historical as $record) {
            // Use the already standardized year
            $populationYear = $record->standardized_year;
            $population = null;
            $populationCount = 1; // Default to 1 to avoid division by zero
            
            if ($populationYear) { // Only query if we have a valid year
                $population = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipality1Name])
                    ->where('year', $populationYear)
                    ->first();
                
                // If no exact match and year is before 2020, use 2020 as closest approximation
                if (!$population && $populationYear < 2020) {
                    $population = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipality1Name])
                        ->where('year', 2020)
                        ->first();
                }
                
                $populationCount = $population ? $population->population : 1;
            }
            
            // Calculate per capita for all key metrics
            $municipality1TrendData['recycling'][] = round($this->currencyToNumeric($record->recycling) / $populationCount, 2);
            $municipality1TrendData['tipping_fees'][] = round($this->currencyToNumeric($record->tipping_fees) / $populationCount, 2);
            $municipality1TrendData['transfer_station_wages'][] = round($this->currencyToNumeric($record->transfer_station_wages) / $populationCount, 2);
            $municipality1TrendData['total_sanitation_refuse'][] = round($this->currencyToNumeric($record->total_sanitation_refuse) / $populationCount, 2);
        }
        
        foreach($municipality2Historical as $record) {
            // Use the already standardized year
            $populationYear = $record->standardized_year;
            $population = null;
            $populationCount = 1; // Default to 1 to avoid division by zero
            
            if ($populationYear) { // Only query if we have a valid year
                $population = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipality2Name])
                    ->where('year', $populationYear)
                    ->first();
                
                // If no exact match and year is before 2020, use 2020 as closest approximation
                if (!$population && $populationYear < 2020) {
                    $population = Population::whereRaw('LOWER(municipality) = LOWER(?)', [$municipality2Name])
                        ->where('year', 2020)
                        ->first();
                }
                
                $populationCount = $population ? $population->population : 1;
            }
            
            // Calculate per capita for all key metrics
            $municipality2TrendData['recycling'][] = round($this->currencyToNumeric($record->recycling) / $populationCount, 2);
            $municipality2TrendData['tipping_fees'][] = round($this->currencyToNumeric($record->tipping_fees) / $populationCount, 2);
            $municipality2TrendData['transfer_station_wages'][] = round($this->currencyToNumeric($record->transfer_station_wages) / $populationCount, 2);
            $municipality2TrendData['total_sanitation_refuse'][] = round($this->currencyToNumeric($record->total_sanitation_refuse) / $populationCount, 2);
        }

        return view('municipalities.compare', compact(
            'municipalities', 
            'commonYears', 
            'municipality1Historical', 
            'municipality2Historical',
            'municipality1Populations',
            'municipality2Populations',
            'municipality1TrendData',
            'municipality2TrendData'
        ));
    }
    
    
    

}
