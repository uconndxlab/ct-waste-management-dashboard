# Implementation Plan

- [x] 1. Create improved regional data aggregation service
  - Create RegionalDataService class with robust aggregation methods that handle all financial fields consistently
  - Implement population aggregation by region for accurate per capita calculations
  - Add support for year-based historical aggregation for trend analysis
  - Optimize with single query approach instead of multiple separate queries
  - _Requirements: 1.1, 2.1, 3.1_

- [x] 1.1 Implement comprehensive financial data aggregation
  - Create method to aggregate all financial fields (recycling, tipping_fees, admin_costs, etc.) by region type
  - Handle currency string conversion consistently across all fields
  - Include municipality count and data availability metrics
  - _Requirements: 1.2, 1.3, 2.2, 2.3, 3.2, 3.3_

- [x] 1.2 Implement population aggregation for per capita calculations
  - Create method to sum population data by region and year
  - Handle missing population data gracefully
  - Support multiple years for historical per capita trends
  - _Requirements: 4.6, 5.6, 6.6_

- [x] 2. Create RegionalController with improved aggregation methods
  - Create RegionalController that uses the new RegionalDataService
  - Implement methods for listing counties, planning regions, and classifications
  - Add method for retrieving detailed regional data for individual region views
  - _Requirements: 1.1, 2.1, 3.1_

- [x] 3. Create unified regional list view template
  - Create single reusable view template that can display any region type (county/planning region/classification)
  - Display region names, municipality counts, aggregated financial totals, and data availability
  - Add comparison checkboxes for selecting exactly 2 regions of the same type
  - Include navigation tabs to switch between different region types
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4, 7.1_

- [x] 4. Implement regional comparison functionality
- [x] 4.1 Add regional comparison method to RegionalController
  - Create comparison logic that aggregates all municipalities within selected regions
  - Calculate accurate per capita values using summed population data by region
  - Generate historical trend data for regions across multiple years
  - Handle validation for exactly 2 regions of the same type
  - _Requirements: 4.2, 4.6, 5.2, 5.6, 6.2, 6.6_

- [x] 4.2 Create regional comparison view
  - Adapt municipalities/compare.blade.php template for regional comparison
  - Display comprehensive per capita comparison table with all financial metrics
  - Show year-over-year trend charts for regional data using Chart.js
  - Calculate and highlight differences between selected regions
  - _Requirements: 4.3, 4.4, 4.5, 5.3, 5.4, 5.5, 6.3, 6.4, 6.5_

- [x] 5. Add routes and navigation for regional functionality
  - Create routes for regional list view with region type parameter
  - Add route for regional comparison POST endpoint with validation
  - Add navigation links from main municipalities page to regional views
  - Add back buttons on comparison pages to return to appropriate regional list
  - _Requirements: 1.1, 2.1, 3.1, 4.2, 5.2, 6.2, 7.1, 7.2, 7.3, 7.4_

- [x] 6. Implement JavaScript for regional comparison selection
  - Adapt the comparison selection JavaScript from municipalities/view-all.blade.php
  - Ensure exactly 2 regions of the same type can be selected for comparison
  - Update button states and selection info text for regional context
  - Handle form submission with region type and selected regions
  - _Requirements: 4.1, 5.1, 6.1_

- [x] 7. Add comprehensive validation and error handling
  - Validate that exactly 2 regions of the same type are selected
  - Handle cases where selected regions don't exist or have insufficient data
  - Display appropriate error messages for invalid comparisons
  - Add graceful handling of missing population or financial data in views
  - _Requirements: 4.2, 5.2, 6.2_

- [ ] 8. Optimize map integration with new aggregation service
  - Update MunicipalityController::showHome() to use RegionalDataService for data retrieval
  - Ensure map tooltips continue showing only the current limited data points (total_refuse, total_admin)
  - Maintain existing map tooltip design and performance while leveraging improved backend aggregation
  - Test that map functionality remains unchanged from user perspective
  - _Requirements: 1.1, 2.1, 3.1_

- [x] 9. Create comprehensive tests for regional functionality
- [x] 9.1 Write unit tests for RegionalDataService
  - Test financial data aggregation accuracy across all fields
  - Test population aggregation and per capita calculations
  - Test historical data retrieval and trend calculations
  - Test error handling for missing or invalid data
  - _Requirements: 1.1, 2.1, 3.1, 4.6, 5.6, 6.6_

- [x] 9.2 Write integration tests for regional views and comparisons
  - Test that regional list views display correct aggregated data
  - Test regional comparison functionality end-to-end with real data
  - Test navigation between different regional views works correctly
  - Test JavaScript comparison selection and form submission
  - _Requirements: 4.1, 4.2, 4.3, 5.1, 5.2, 5.3, 6.1, 6.2, 6.3_