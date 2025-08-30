<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\RegionalDataService;
use App\Models\Municipality;
use App\Models\TownClassification;
use App\Models\Population;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class RegionalDataServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RegionalDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RegionalDataService();
        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // Create test town classifications
        TownClassification::create([
            'municipality' => 'Test Town 1',
            'county' => 'Test County',
            'geographical_region' => 'Test Planning Region',
            'region_type' => 'Urban'
        ]);

        TownClassification::create([
            'municipality' => 'Test Town 2',
            'county' => 'Test County',
            'geographical_region' => 'Test Planning Region',
            'region_type' => 'Urban'
        ]);

        TownClassification::create([
            'municipality' => 'Rural Town 1',
            'county' => 'Rural County',
            'geographical_region' => 'Rural Planning Region',
            'region_type' => 'Rural'
        ]);

        // Create test municipalities with financial data
        Municipality::create([
            'name' => 'Test Town 1',
            'year' => 2023,
            'bulky_waste' => '1000.00',
            'recycling' => '500.00',
            'tipping_fees' => '300.00',
            'admin_costs' => '200.00',
            'hazardous_waste' => '100.00',
            'contractual_services' => '150.00',
            'landfill_costs' => '400.00',
            'total_sanitation_refuse' => '2650.00',
            'only_public_works' => '800.00',
            'transfer_station_wages' => '250.00',
            'hauling_fees' => '350.00',
            'curbside_pickup_fees' => '200.00',
            'waste_collection' => '600.00'
        ]);

        Municipality::create([
            'name' => 'Test Town 2',
            'year' => 2023,
            'bulky_waste' => '800.00',
            'recycling' => '400.00',
            'tipping_fees' => '250.00',
            'admin_costs' => '150.00',
            'hazardous_waste' => '80.00',
            'contractual_services' => '120.00',
            'landfill_costs' => '350.00',
            'total_sanitation_refuse' => '2150.00',
            'only_public_works' => '600.00',
            'transfer_station_wages' => '200.00',
            'hauling_fees' => '300.00',
            'curbside_pickup_fees' => '180.00',
            'waste_collection' => '500.00'
        ]);

        Municipality::create([
            'name' => 'Rural Town 1',
            'year' => 2023,
            'bulky_waste' => '200.00',
            'recycling' => '100.00',
            'tipping_fees' => '50.00',
            'admin_costs' => '30.00',
            'hazardous_waste' => '20.00',
            'contractual_services' => '25.00',
            'landfill_costs' => '80.00',
            'total_sanitation_refuse' => '505.00',
            'only_public_works' => '150.00',
            'transfer_station_wages' => '40.00',
            'hauling_fees' => '60.00',
            'curbside_pickup_fees' => '35.00',
            'waste_collection' => '120.00'
        ]);

        // Create test population data
        Population::create([
            'municipality' => 'Test Town 1',
            'year' => 2023,
            'population' => 10000
        ]);

        Population::create([
            'municipality' => 'Test Town 2',
            'year' => 2023,
            'population' => 8000
        ]);

        Population::create([
            'municipality' => 'Rural Town 1',
            'year' => 2023,
            'population' => 2000
        ]);

        // Historical data will be added in specific tests that need it
    }

    /** @test */
    public function it_aggregates_county_totals_correctly()
    {
        $countyTotals = $this->service->getCountyTotals();

        $this->assertInstanceOf(Collection::class, $countyTotals);
        
        // Debug: Check what counties we actually have
        $countyNames = $countyTotals->keys()->toArray();
        $this->assertContains('Test County', $countyNames, 'Test County should exist in results');
        $this->assertContains('Rural County', $countyNames, 'Rural County should exist in results');

        $testCounty = $countyTotals->get('Test County');
        $this->assertNotNull($testCounty, 'Test County data should not be null');
        $this->assertEquals(2, $testCounty->total_municipalities, 'Test County should have 2 municipalities');
        $this->assertEquals(2, $testCounty->municipalities_with_data, 'Test County should have 2 municipalities with data');
        
        // Debug: Check actual values
        $actualBulkyWaste = (float) $testCounty->total_bulky_waste;
        $this->assertEquals(1800.00, $actualBulkyWaste, "Expected 1800.00 but got {$actualBulkyWaste} for bulky waste");
        
        $actualRecycling = (float) $testCounty->total_recycling;
        $this->assertEquals(900.00, $actualRecycling, "Expected 900.00 but got {$actualRecycling} for recycling");
        
        $actualSanitation = (float) $testCounty->total_total_sanitation_refuse;
        $this->assertEquals(4800.00, $actualSanitation, "Expected 4800.00 but got {$actualSanitation} for total sanitation");
    }

    /** @test */
    public function it_aggregates_planning_region_totals_correctly()
    {
        $planningRegionTotals = $this->service->getPlanningRegionTotals();

        $this->assertInstanceOf(Collection::class, $planningRegionTotals);
        $this->assertCount(2, $planningRegionTotals); // Test Planning Region and Rural Planning Region

        $testRegion = $planningRegionTotals->get('Test Planning Region');
        $this->assertNotNull($testRegion);
        $this->assertEquals(2, $testRegion->total_municipalities);
        $this->assertEquals(2, $testRegion->municipalities_with_data);
        $this->assertEquals(1800.00, $testRegion->total_bulky_waste);
        $this->assertEquals(900.00, $testRegion->total_recycling);
    }

    /** @test */
    public function it_aggregates_classification_totals_correctly()
    {
        $classificationTotals = $this->service->getClassificationTotals();

        $this->assertInstanceOf(Collection::class, $classificationTotals);
        $this->assertCount(2, $classificationTotals); // Urban and Rural

        $urbanClassification = $classificationTotals->get('Urban');
        $this->assertNotNull($urbanClassification);
        $this->assertEquals(2, $urbanClassification->total_municipalities);
        $this->assertEquals(2, $urbanClassification->municipalities_with_data);
        $this->assertEquals(1800.00, $urbanClassification->total_bulky_waste);

        $ruralClassification = $classificationTotals->get('Rural');
        $this->assertNotNull($ruralClassification);
        $this->assertEquals(1, $ruralClassification->total_municipalities);
        $this->assertEquals(1, $ruralClassification->municipalities_with_data);
        $this->assertEquals(200.00, $ruralClassification->total_bulky_waste);
    }

    /** @test */
    public function it_gets_specific_region_totals_correctly()
    {
        // Test county format
        $countyData = $this->service->getRegionTotals('county', 'Test County');
        $this->assertNotNull($countyData);
        $this->assertEquals(2, $countyData->total_municipalities);
        $this->assertEquals(1800.00, $countyData->total_bulky_waste);

        // Test planning-region format (hyphenated)
        $planningRegionData = $this->service->getRegionTotals('planning-region', 'Test Planning Region');
        $this->assertNotNull($planningRegionData);
        $this->assertEquals(2, $planningRegionData->total_municipalities);
        $this->assertEquals(1800.00, $planningRegionData->total_bulky_waste);

        // Test planning_region format (underscored)
        $planningRegionData2 = $this->service->getRegionTotals('planning_region', 'Test Planning Region');
        $this->assertNotNull($planningRegionData2);
        $this->assertEquals(2, $planningRegionData2->total_municipalities);

        // Test classification
        $classificationData = $this->service->getRegionTotals('classification', 'Urban');
        $this->assertNotNull($classificationData);
        $this->assertEquals(2, $classificationData->total_municipalities);
        $this->assertEquals(1800.00, $classificationData->total_bulky_waste);
    }

    /** @test */
    public function it_handles_invalid_region_types_gracefully()
    {
        $result = $this->service->getRegionTotals('invalid_type', 'Test Region');
        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_nonexistent_regions_gracefully()
    {
        $result = $this->service->getRegionTotals('county', 'Nonexistent County');
        $this->assertNull($result);
    }

    /** @test */
    public function it_aggregates_population_data_correctly()
    {
        $countyPopulations = $this->service->getCountyPopulationTotals(2023);

        $this->assertInstanceOf(Collection::class, $countyPopulations);
        
        $testCountyPop = $countyPopulations->where('county', 'Test County')->first();
        $this->assertNotNull($testCountyPop);
        $this->assertEquals(18000, $testCountyPop->total_population); // 10000 + 8000
        $this->assertEquals(2, $testCountyPop->municipalities_with_population_data);

        $ruralCountyPop = $countyPopulations->where('county', 'Rural County')->first();
        $this->assertNotNull($ruralCountyPop);
        $this->assertEquals(2000, $ruralCountyPop->total_population);
        $this->assertEquals(1, $ruralCountyPop->municipalities_with_population_data);
    }

    /** @test */
    public function it_calculates_per_capita_values_correctly()
    {
        $regionData = (object) [
            'total_bulky_waste' => 1800.00,
            'total_recycling' => 900.00,
            'total_tipping_fees' => 550.00
        ];

        $result = $this->service->calculateRegionalPerCapita($regionData, 18000);

        $this->assertEquals(0.10, $result->total_bulky_waste_per_capita); // 1800 / 18000
        $this->assertEquals(0.05, $result->total_recycling_per_capita); // 900 / 18000
        $this->assertEquals(0.03, $result->total_tipping_fees_per_capita); // 550 / 18000
    }

    /** @test */
    public function it_handles_null_population_for_per_capita_calculations()
    {
        $regionData = (object) [
            'total_bulky_waste' => 1800.00,
            'total_recycling' => 900.00
        ];

        $result = $this->service->calculateRegionalPerCapita($regionData, null);

        $this->assertNull($result->total_bulky_waste_per_capita);
        $this->assertNull($result->total_recycling_per_capita);
    }

    /** @test */
    public function it_handles_zero_population_for_per_capita_calculations()
    {
        $regionData = (object) [
            'total_bulky_waste' => 1800.00,
            'total_recycling' => 900.00
        ];

        $result = $this->service->calculateRegionalPerCapita($regionData, 0);

        $this->assertNull($result->total_bulky_waste_per_capita);
        $this->assertNull($result->total_recycling_per_capita);
    }

    /** @test */
    public function it_retrieves_historical_data_correctly()
    {
        // Add historical data for this specific test
        Municipality::create([
            'name' => 'Test Town 1',
            'year' => 2022,
            'bulky_waste' => '950.00',
            'recycling' => '480.00',
            'total_sanitation_refuse' => '2500.00'
        ]);

        Population::create([
            'municipality' => 'Test Town 1',
            'year' => 2022,
            'population' => 9800
        ]);

        $historicalData = $this->service->getRegionalHistoricalData('county', 'Test County');

        $this->assertInstanceOf(Collection::class, $historicalData);
        $this->assertCount(2, $historicalData); // 2022 and 2023 data

        $data2023 = $historicalData->where('year', 2023)->first();
        $this->assertNotNull($data2023);
        $this->assertEquals(1800.00, $data2023->total_bulky_waste);

        $data2022 = $historicalData->where('year', 2022)->first();
        $this->assertNotNull($data2022);
        $this->assertEquals(950.00, $data2022->total_bulky_waste);
    }

    /** @test */
    public function it_gets_available_years_correctly()
    {
        // Add historical data for this specific test
        Municipality::create([
            'name' => 'Test Town 1',
            'year' => 2022,
            'bulky_waste' => '950.00'
        ]);

        $availableYears = $this->service->getRegionAvailableYears('county', 'Test County');

        $this->assertInstanceOf(Collection::class, $availableYears);
        
        // Debug: Check what years we actually get
        $yearsArray = $availableYears->toArray();
        $this->assertCount(2, $availableYears, 'Expected 2 years but got: ' . implode(', ', $yearsArray));
        $this->assertContains(2022, $availableYears, 'Should contain 2022. Available years: ' . implode(', ', $yearsArray));
        $this->assertContains(2023, $availableYears, 'Should contain 2023. Available years: ' . implode(', ', $yearsArray));
    }

    /** @test */
    public function it_validates_region_for_comparison_correctly()
    {
        // Valid region with data
        $validation = $this->service->validateRegionForComparison('county', 'Test County');
        $this->assertTrue($validation['valid']);
        $this->assertEquals('', $validation['message']);

        // Invalid region type
        $validation = $this->service->validateRegionForComparison('invalid_type', 'Test County');
        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('not found', $validation['message']);

        // Nonexistent region
        $validation = $this->service->validateRegionForComparison('county', 'Nonexistent County');
        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('not found', $validation['message']);
    }

    /** @test */
    public function it_handles_empty_region_names_gracefully()
    {
        $result = $this->service->getRegionTotals('county', '');
        $this->assertNull($result);

        $result = $this->service->getRegionTotals('county', '   ');
        $this->assertNull($result);
    }

    /** @test */
    public function it_filters_out_empty_region_names_in_aggregation()
    {
        // Create a classification with empty region name
        TownClassification::create([
            'municipality' => 'Empty Region Town',
            'county' => '',
            'geographical_region' => '',
            'region_type' => ''
        ]);

        $countyTotals = $this->service->getCountyTotals();
        $planningRegionTotals = $this->service->getPlanningRegionTotals();
        $classificationTotals = $this->service->getClassificationTotals();

        // Should not include empty region names
        $this->assertFalse($countyTotals->has(''));
        $this->assertFalse($planningRegionTotals->has(''));
        $this->assertFalse($classificationTotals->has(''));
    }
}