# Requirements Document

## Introduction

This feature will extend the existing municipality comparison functionality to allow users to compare regions (counties, planning regions, or rural/urban classifications) instead of individual municipalities. Users will be able to view lists of regions and compare two regions using the same comparison interface and metrics currently used for municipalities, but with aggregated data from all municipalities within each region.

## Requirements

### Requirement 1

**User Story:** As a waste management analyst, I want to view a list of all counties in Connecticut, so that I can see which counties are available for comparison.

#### Acceptance Criteria

1. WHEN I navigate to a counties list page THEN the system SHALL display all unique counties from the town_classifications table
2. WHEN I view the counties list THEN the system SHALL show the county name and total number of municipalities in each county
3. WHEN I view the counties list THEN the system SHALL show aggregated financial data (total sanitation refuse, admin costs) for each county
4. WHEN I view the counties list THEN the system SHALL show how many municipalities have data vs total municipalities in each county
5. WHEN I click on a county name THEN the system SHALL navigate to a detailed county view

### Requirement 2

**User Story:** As a waste management analyst, I want to view a list of all planning regions in Connecticut, so that I can see which planning regions are available for comparison.

#### Acceptance Criteria

1. WHEN I navigate to a planning regions list page THEN the system SHALL display all unique geographical_region values from the town_classifications table
2. WHEN I view the planning regions list THEN the system SHALL show the region name and total number of municipalities in each region
3. WHEN I view the planning regions list THEN the system SHALL show aggregated financial data (total sanitation refuse, admin costs) for each region
4. WHEN I view the planning regions list THEN the system SHALL show how many municipalities have data vs total municipalities in each region
5. WHEN I click on a planning region name THEN the system SHALL navigate to a detailed planning region view

### Requirement 3

**User Story:** As a waste management analyst, I want to view a list of rural/urban classifications, so that I can see which classification types are available for comparison.

#### Acceptance Criteria

1. WHEN I navigate to a classifications list page THEN the system SHALL display all unique region_type values from the town_classifications table
2. WHEN I view the classifications list THEN the system SHALL show the classification name and total number of municipalities in each classification
3. WHEN I view the classifications list THEN the system SHALL show aggregated financial data (total sanitation refuse, admin costs) for each classification
4. WHEN I view the classifications list THEN the system SHALL show how many municipalities have data vs total municipalities in each classification
5. WHEN I click on a classification name THEN the system SHALL navigate to a detailed classification view

### Requirement 4

**User Story:** As a waste management analyst, I want to compare two counties, so that I can analyze differences in waste management costs and efficiency between counties.

#### Acceptance Criteria

1. WHEN I am on the counties list page THEN the system SHALL provide checkboxes to select exactly two counties for comparison
2. WHEN I select two counties and click compare THEN the system SHALL navigate to a county comparison page
3. WHEN I view the county comparison page THEN the system SHALL display the same comparison interface as municipality comparison
4. WHEN I view the county comparison page THEN the system SHALL show aggregated per capita metrics for both counties
5. WHEN I view the county comparison page THEN the system SHALL show year-over-year trend charts for both counties if historical data exists
6. WHEN I view the county comparison page THEN the system SHALL calculate per capita values using the sum of populations from all municipalities in each county

### Requirement 5

**User Story:** As a waste management analyst, I want to compare two planning regions, so that I can analyze differences in waste management approaches between different planning regions.

#### Acceptance Criteria

1. WHEN I am on the planning regions list page THEN the system SHALL provide checkboxes to select exactly two regions for comparison
2. WHEN I select two regions and click compare THEN the system SHALL navigate to a planning region comparison page
3. WHEN I view the planning region comparison page THEN the system SHALL display the same comparison interface as municipality comparison
4. WHEN I view the planning region comparison page THEN the system SHALL show aggregated per capita metrics for both regions
5. WHEN I view the planning region comparison page THEN the system SHALL show year-over-year trend charts for both regions if historical data exists
6. WHEN I view the planning region comparison page THEN the system SHALL calculate per capita values using the sum of populations from all municipalities in each region

### Requirement 6

**User Story:** As a waste management analyst, I want to compare rural vs urban classifications, so that I can analyze differences in waste management costs between rural and urban areas.

#### Acceptance Criteria

1. WHEN I am on the classifications list page THEN the system SHALL provide checkboxes to select exactly two classifications for comparison
2. WHEN I select two classifications and click compare THEN the system SHALL navigate to a classification comparison page
3. WHEN I view the classification comparison page THEN the system SHALL display the same comparison interface as municipality comparison
4. WHEN I view the classification comparison page THEN the system SHALL show aggregated per capita metrics for both classifications
5. WHEN I view the classification comparison page THEN the system SHALL show year-over-year trend charts for both classifications if historical data exists
6. WHEN I view the classification comparison page THEN the system SHALL calculate per capita values using the sum of populations from all municipalities in each classification

### Requirement 7

**User Story:** As a user, I want to easily navigate between different regional views, so that I can efficiently explore different ways of analyzing the data.

#### Acceptance Criteria

1. WHEN I am on any regional list page THEN the system SHALL provide navigation links to switch between counties, planning regions, and classifications views
2. WHEN I am on any regional comparison page THEN the system SHALL provide a back button to return to the appropriate regional list
3. WHEN I am on the main municipalities page THEN the system SHALL provide links to access regional views
4. WHEN I am on any regional page THEN the system SHALL maintain consistent navigation patterns with the existing municipality pages