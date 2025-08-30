<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\RegionalDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegionalValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $regionalDataService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regionalDataService = new RegionalDataService();
    }

    /** @test */
    public function it_validates_invalid_region_type()
    {
        $validation = $this->regionalDataService->validateRegionForComparison('invalid_type', 'Test Region');
        
        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('not found', $validation['message']);
    }

    /** @test */
    public function it_validates_empty_region_name()
    {
        $validation = $this->regionalDataService->validateRegionForComparison('county', '');
        
        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('not found', $validation['message']);
    }

    /** @test */
    public function it_validates_nonexistent_region()
    {
        $validation = $this->regionalDataService->validateRegionForComparison('county', 'Nonexistent County');
        
        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('not found', $validation['message']);
    }

    /** @test */
    public function regional_comparison_requires_exactly_two_regions()
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
            'regions' => ['Test County']
        ]);

        $response->assertSessionHasErrors(['regions']);

        // Test with three regions
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['County 1', 'County 2', 'County 3']
        ]);

        $response->assertSessionHasErrors(['regions']);
    }

    /** @test */
    public function regional_comparison_requires_valid_region_type()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'invalid_type',
            'regions' => ['Region 1', 'Region 2']
        ]);

        $response->assertSessionHasErrors(['region_type']);
    }

    /** @test */
    public function regional_comparison_prevents_comparing_same_region()
    {
        $response = $this->post(route('regions.compare'), [
            'region_type' => 'county',
            'regions' => ['Same County', 'Same County']
        ]);

        $response->assertSessionHasErrors(['regions']);
        $response->assertSessionHasErrorsIn('default', ['regions' => 'You cannot compare a region with itself']);
    }
}