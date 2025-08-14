<?php
function generateBreadcrumbs($url)
{
    $segments = explode('/', trim(parse_url($url, PHP_URL_PATH), '/'));
    $breadcrumbs = [];
    $currentUrl = '';

    $customNames = [
        '' => 'Home /',
        'municipalities' => 'Municipalities',
        'regions' => 'Regional Analysis',
        'county' => 'Counties',
        'planning-region' => 'Planning Regions',
        'classification' => 'Classifications',
        'compare' => 'Comparison',
    ];

    foreach ($segments as $segment) {
        $currentUrl .= '/' . $segment;

        // Skip empty segments (root URL)
        if ($segment === '') {
            continue;
        }

        $breadcrumbs[] = [
            'name' => $customNames[$segment] ?? ucfirst($segment),
            'url' => $currentUrl,
        ];
    }

    return $breadcrumbs;
}