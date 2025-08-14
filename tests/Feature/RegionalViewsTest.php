<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Municipality;
use App\Models\TownClassification;
use App\Models\Population;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegionalViewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // Create test town classifications
        TownClassification::create([
            'municipality' => 'Hartford',
            'county' => 'Hartford County',
            'geographical_region' => 'Capitol',
            'region_type' => 'Urban'
        ]);

        TownClassification::create([
            'municipality' => 'West Hartford',
            'county' => 'Hartford County',
            'geographical_region' => 'Capitol',
            'region_type' => 'Urban'
        ]);

        TownClassification::create([
            'municipality' => 'Bridgeport',
            'county' => 'Fairfield County',
            'geographical_region' => 'Greater Bridgeport',
            'region_type' => 'Urban'
        ]);

        TownClassification::create([
            'municipality' => 'Rural Town',
            'county' => 'Litchfield County',
            'geographical_region' => 'Northwest Hills',
            'region_type' => 'Rural'
        ]);

        // Create test municipalities with financial data
        Municipality::create([
            'name' => 'Hartford',
            'year' => 2023,
            'bulky_waste' => '2000.00',
            'recycling' => '1500.00',
            'tipping_fees' => '800.00',
            'admin_costs' => '600.00',
            'total_sanitation_refuse' => '5000.00'
        ]);

        Municipality::create([
            'name' => 'West Hartford',
            'year' => 2023,
            'bulky_waste' => '1800.00',
            'recycling' => '1200.00',
            'tipping_fees' => '700.00',
            'admin_costs' => '500.00',
            'total_sanitation_refuse' => '4500.00'
        ]);

        Municipality::create([
            'name' => 'Bridgeport',
            'year' => 2023,
            'bulky_waste' => '3000.00',
            'recycling' => '2000.00',
            'tipping_fees' => '1200.00',
            'admin_costs' => '800.00',
            'total_sanitation_refuse' => '7500.00'
        ]);

        Municipality::create([
            'name' => 'Rural Town',
            'year' => 2023,
            'bulky_waste' => '300.00',
            'recycling' => '200.00',
            'tipping_fees' => '100.00',
            'admin_costs' => '80.00',
            'total_sanitation_refuse' => '750.00'
        ]);

        // Create population data
        Population::create([
            'municipality' => 'Hartford',
            'year' => 2023,
            'population' => 120000
        ]);

        Population::create([
            'municipality' => 'West Hartford',
            'year' => 2023,
            'population' => 65000
        ]);

        Population::create([
            'municipality' => 'Bridgeport',
            'year' => 2023,
            'population' => 145000
        ]);

        Population::create([
            'municipality' => 'Rural Town',
            'year' => 2023,
            'population' => 5000
        ]);
    }

    /** @test */
    public function it_displays_counties_list_correctly()
    {
        $response = $this->get(route('regions.counties'));

        $response->assertStatus(200);
        $response->assertViewIs('regions.list');
        $response->assertViewHas('regionType', 'county');
        $response->assertViewHas('regions');
        
        // Check that counties are displayed
        $response->assertSee('Hartford County');
        $response->assertSee('Fairfield County');
        $response->assertSee('Litchfield County');
        
        // Check that municipality counts are displayed
        $response->assertSee('2'); // Hartford County has 2 municipalities
        $response->assertSee('1'); // Fairfield and Litchfield each have 1
    }

    /** @test */
    public function it_displays_planning_regions_list_correctly()
    {
        $response = $this->get(route('regions.planning-regions'));

        $response->assertStatus(200);
        $response->assertViewIs('regions.list');
        $response->assertViewHas('regionType', 'planning-region');
        $response->assertViewHas('regions');
        
        // Check that planning regions are displayed
        $response->assertSee('Capitol');
        $response->assertSee('Greater Bridgeport');
        $response->assertSee('Northwest Hills');
    }

    /** @test */
    public function it_displays_classifications_list_correctly()
    {
        $response = $this->get(route('regions.classifications'));

        $response->assertStatus(200);
        $response->assertViewIs('regions.list');
        $response->assertViewHas('regionType', 'classification');
        $response->assertViewHas('regions');
        
        // Check that classifications are displayed
        $response->assertSee('Urban');
        $response->assertSee('Rural');
        
        // Urban should have 3 municipalities, Rural should have 1
        $response->assertSee('3');
        $response->assertSee('1');
    }

    /** @test */
    public function it_displays_navigation_tabs_correctly()
    {
        $response = $this->get(route('regions.counties'));

        $response->assertStatus(200);
        
        // Check navigation tabs are present
        $response->assertSee('Counties');
        $response->assertSee('Planning Regions');
        $response->assertSee('Classifications');
        
        // Check that the correct tab is active
        $response->assertSee('nav-link active', false); // Counties tab should be active
    }

    /** @test */
    public function it_displays_comparison_form_correctly()
    {
        $response = $this->get(route('regions.counties'));

        $response->assertStatus(200);
        
        // Check comparison form elements
        $response->assertSee('Compare');
        $response->assertSee('Select 2');
        $response->assertSee('compare-form');
        $response->assertSee('region_type');
    }

    /** @test */
    public function it_handles_regional_comparison_with_valid_data()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Fairfield County']
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('regions.compare');
        $response->assertViewHas('regions');
        $response->assertViewHas('regionType', 'county');
        
        // Check that both regions are displayed in comparison
        $response->assertSee('Hartford County');
        $response->assertSee('Fairfield County');
        
        // Check that comparison data is displayed
        $response->assertSee('Per Capita Comparison');
        $response->assertSee('Total Population');
        $response->assertSee('Bulky Waste per Capita');
    }

    /** @test */
    public function it_validates_exactly_two_regions_for_comparison()
    {
        // Test with no regions
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => []
        ]);

        $response->assertSessionHasErrors(['regions']);

        // Test with one region
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County']
        ]);

        $response->assertSessionHasErrors(['regions']);

        // Test with three regions
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Fairfield County', 'Litchfield County']
        ]);

        $response->assertSessionHasErrors(['regions']);
    }

    /** @test */
    public function it_validates_region_type_for_comparison()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'invalid_type',
            'regions' => ['Hartford County', 'Fairfield County']
        ]);

        $response->assertSessionHasErrors(['region_type']);
    }

    /** @test */
    public function it_prevents_comparing_same_region()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Hartford County']
        ]);

        $response->assertSessionHasErrors(['regions']);
        $response->assertSessionHasErrorsIn('default', ['regions']);
    }

    /** @test */
    public function it_handles_nonexistent_regions_in_comparison()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Nonexistent County']
        ]);

        $response->assertSessionHasErrors(['regions']);
    }

    /** @test */
    public function it_supports_both_planning_region_formats()
    {
        // Test hyphenated format
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'planning-region',
            'regions' => ['Capitol', 'Greater Bridgeport']
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('regions.compare');

        // Test underscored format (should also work)
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'planning_region',
            'regions' => ['Capitol', 'Greater Bridgeport']
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('regions.compare');
    }

    /** @test */
    public function it_displays_per_capita_calculations_correctly()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Fairfield County']
        ]);

        $response->assertStatus(200);
        
        // Check that per capita values are calculated and displayed
        $response->assertSee('per Capita');
        $response->assertSee('$'); // Should show dollar amounts
        
        // Check population totals are displayed
        $response->assertSee('185,000'); // Hartford County: 120,000 + 65,000
        $response->assertSee('145,000'); // Fairfield County: 145,000
    }

    /** @test */
    public function it_handles_missing_population_data_gracefully()
    {
        // Remove population data for one municipality
        Population::where('municipality', 'Hartford')->delete();

        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Fairfield County']
        ]);

        $response->assertStatus(200);
        
        // Should still display comparison but handle missing data
        $response->assertSee('No data available');
    }

    /** @test */
    public function it_displays_data_quality_warnings()
    {
        // Create a region with no financial data
        TownClassification::create([
            'municipality' => 'No Data Town',
            'county' => 'Empty County',
            'geographical_region' => 'Empty Region',
            'region_type' => 'Urban'
        ]);

        $response = $this->get(route('regions.counties'));

        $response->assertStatus(200);
        
        // Should show data quality warnings
        $response->assertSee('Data Quality Notice');
    }

    /** @test */
    public function it_displays_trend_charts_when_historical_data_available()
    {
        // Add historical data
        Municipality::create([
            'name' => 'Hartford',
            'year' => 2022,
            'bulky_waste' => '1900.00',
            'recycling' => '1400.00',
            'total_sanitation_refuse' => '4800.00'
        ]);

        Municipality::create([
            'name' => 'Bridgeport',
            'year' => 2022,
            'bulky_waste' => '2800.00',
            'recycling' => '1900.00',
            'total_sanitation_refuse' => '7200.00'
        ]);

        Population::create([
            'municipality' => 'Hartford',
            'year' => 2022,
            'population' => 118000
        ]);

        Population::create([
            'municipality' => 'Bridgeport',
            'year' => 2022,
            'population' => 143000
        ]);

        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Fairfield County']
        ]);

        $response->assertStatus(200);
        
        // Should display trend charts
        $response->assertSee('Year-over-Year Trends');
        $response->assertSee('Chart.js');
        $response->assertSee('recyclingChart');
        $response->assertSee('tippingChart');
    }

    /** @test */
    public function it_handles_insufficient_historical_data_gracefully()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Fairfield County']
        ]);

        $response->assertStatus(200);
        
        // Should show message about insufficient historical data
        $response->assertSee('No Common Historical Data');
    }

    /** @test */
    public function it_displays_correct_region_type_labels()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'planning-region',
            'regions' => ['Capitol', 'Greater Bridgeport']
        ]);

        $response->assertStatus(200);
        $response->assertSee('Planning Regions'); // Should show plural form in comparison
    }

    /** @test */
    public function it_handles_regions_with_no_municipalities()
    {
        // Create a classification with no associated municipalities
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County', 'Nonexistent County']
        ]);

        $response->assertSessionHasErrors(['regions']);
        $response->assertSessionHasErrorsIn('default', ['regions' => 'do not exist']);
    }

    /** @test */
    public function it_preserves_form_input_on_validation_errors()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Hartford County'] // Only one region
        ]);

        $response->assertSessionHasErrors(['regions']);
        $response->assertRedirect();
        
        // Should preserve the input
        $this->assertEquals('county', session()->getOldInput('region_type'));
        $this->assertEquals(['Hartford County'], session()->getOldInput('regions'));
    }
}