# Design Document

## Overview

The regional comparison feature extends the existing municipality comparison system to support comparing regions (counties, planning regions, and rural/urban classifications). The design leverages the existing aggregation logic already implemented in the `MunicipalityController::showHome()` method and reuses the comparison UI patterns from the municipality comparison feature.

## Architecture

### Controller Structure

**New Controller: `RegionalController`**
- Handles all regional list views and comparisons
- Reuses existing aggregation queries from `MunicipalityController`
- Follows the same patterns as `MunicipalityController` for consistency

**Key Methods:**
- `listCounties()` - Display county list with aggregated data
- `listPlanningRegions()` - Display planning region list with aggregated data  
- `listClassifications()` - Display rural/urban classification list with aggregated data
- `compareRegions()` - Handle regional comparison logic
- `viewRegion()` - Display detailed view of a specific region

### Route Structure

Following the existing pattern, new routes will be organized under `/regions`:

```php
// Regional list views
Route::get('/regions/counties', [RegionalController::class, 'listCounties'])->name('regions.counties');
Route::get('/regions/planning-regions', [RegionalController::class, 'listPlanningRegions'])->name('regions.planning-regions');
Route::get('/regions/classifications', [RegionalController::class, 'listClassifications'])->name('regions.classifications');

// Regional detail views
Route::get('/regions/counties/{county}', [RegionalController::class, 'viewRegion'])->name('regions.county.view');
Route::get('/regions/planning-regions/{region}', [RegionalController::class, 'viewRegion'])->name('regions.planning-region.view');
Route::get('/regions/classifications/{classification}', [RegionalController::class, 'viewRegion'])->name('regions.classification.view');

// Regional comparison
Route::post('/regions/compare', [RegionalController::class, 'compareRegions'])->name('regions.compare');
```

## Components and Interfaces

### Data Aggregation Service

**RegionalDataService Class**
- Centralizes the aggregation logic currently in `MunicipalityController::showHome()`
- Provides methods for calculating regional totals, per capita values, and historical trends
- Handles population aggregation for per capita calculations

**Key Methods:**
```php
public function getCountyTotals(): Collection
public function getPlanningRegionTotals(): Collection  
public function getClassificationTotals(): Collection
public function getRegionalHistoricalData(string $regionType, string $regionName): Collection
public function calculateRegionalPerCapita(Collection $municipalities, Collection $populations): array
```

### View Components

**Regional List Views**
- `resources/views/regions/counties.blade.php`
- `resources/views/regions/planning-regions.blade.php`
- `resources/views/regions/classifications.blade.php`

**Regional Detail Views**
- `resources/views/regions/view-region.blade.php` (shared template)

**Regional Comparison View**
- `resources/views/regions/compare.blade.php` (adapted from municipality compare)

### UI Patterns

**List View Structure:**
- Follows the same layout as `municipalities/view-all.blade.php`
- Shows region name, total municipalities, municipalities with data, aggregated totals
- Includes comparison checkboxes (select exactly 2)
- Provides navigation between different regional views

**Comparison View Structure:**
- Reuses the exact same layout and JavaScript from `municipalities/compare.blade.php`
- Shows per capita comparison table
- Displays year-over-year trend charts
- Calculates differences between regions

## Data Models

### No New Models Required

The existing models are sufficient:
- `Municipality` - Contains financial data
- `TownClassification` - Contains regional classifications
- `Population` - Contains population data for per capita calculations

### Data Relationships

**Regional Aggregation Logic:**
```sql
-- County totals (already implemented)
SELECT 
    town_classifications.county,
    SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.total_sanitation_refuse, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_refuse,
    SUM(CAST(REPLACE(REPLACE(COALESCE(municipalities.admin_costs, '0'), '$', ''), ',', '') AS DECIMAL(15,2))) as total_admin,
    COUNT(DISTINCT municipalities.name) as total_municipalities,
    COUNT(DISTINCT CASE WHEN municipalities.total_sanitation_refuse IS NOT NULL THEN municipalities.name END) as municipalities_with_data
FROM municipalities 
LEFT JOIN town_classifications ON municipalities.name = town_classifications.municipality
WHERE town_classifications.county IS NOT NULL
GROUP BY town_classifications.county
```

**Population Aggregation:**
```sql
-- Sum populations by region for per capita calculations
SELECT 
    town_classifications.county,
    populations.year,
    SUM(populations.population) as total_population
FROM populations
JOIN town_classifications ON populations.municipality = town_classifications.municipality  
WHERE town_classifications.county IS NOT NULL
GROUP BY town_classifications.county, populations.year
```

## Error Handling

### Validation Rules

**Regional Comparison Validation:**
- Exactly 2 regions must be selected
- Both regions must exist in the database
- Both regions must have at least some financial data

**Error Messages:**
- "You must select exactly two regions for comparison."
- "Could not find data for the selected regions."
- "Selected regions do not have sufficient data for comparison."

### Missing Data Handling

**Graceful Degradation:**
- Show $0 for missing financial data
- Show "No data available" for missing population data
- Hide trend charts if insufficient historical data
- Display data availability indicators

## Testing Strategy

### Unit Tests

**RegionalDataService Tests:**
- Test aggregation calculations with known data sets
- Test per capita calculations with various population scenarios
- Test handling of missing/null data
- Test year-over-year trend calculations

**RegionalController Tests:**
- Test list view data retrieval
- Test comparison validation logic
- Test region detail view data
- Test error handling for invalid regions

### Integration Tests

**Regional List Views:**
- Test that all regions are displayed correctly
- Test aggregated totals match expected values
- Test navigation between different regional views
- Test comparison checkbox functionality

**Regional Comparison:**
- Test comparison with valid region pairs
- Test per capita calculations match expected values
- Test trend chart data generation
- Test comparison with regions having different data availability

### Browser Tests

**User Workflow Tests:**
- Navigate to regional lists → select regions → compare → view results
- Test responsive design on different screen sizes
- Test JavaScript functionality for comparison selection
- Test chart rendering and interactivity

## Implementation Notes

### Reuse Existing Code

**Leverage Current Aggregation:**
The `MunicipalityController::showHome()` method already contains the exact aggregation logic needed:
- `$countyTotals` - Ready to use for county comparisons
- `$regionTotals` - Ready to use for planning region comparisons  
- `$typeTotals` - Ready to use for classification comparisons

**Reuse Comparison Logic:**
The `MunicipalityController::compareMunicipalities()` method provides the template for:
- Per capita calculations
- Historical data retrieval
- Trend chart data preparation
- Comparison view data structure

**Reuse UI Components:**
- Copy and adapt `municipalities/view-all.blade.php` for regional lists
- Copy and adapt `municipalities/compare.blade.php` for regional comparison
- Reuse the same JavaScript for comparison selection and chart rendering

### Performance Considerations

**Database Optimization:**
- The aggregation queries are already optimized in the existing code
- Consider adding database indexes on `town_classifications.county`, `town_classifications.geographical_region`, `town_classifications.region_type`
- Cache regional totals if performance becomes an issue

**Frontend Optimization:**
- Reuse existing Chart.js setup and configuration
- Lazy load trend charts only when comparison data is available
- Minimize JavaScript bundle size by reusing existing comparison scripts