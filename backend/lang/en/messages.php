<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Messages - English
    |--------------------------------------------------------------------------
    */

    // General validation
    'validation_error' => 'Validation error',
    'invalid_request' => 'Invalid request',

    // Authentication
    'invalid_credentials' => 'Invalid credentials',
    'logout_success' => 'Successfully logged out',
    'unauthorized' => 'Unauthorized',
    'token_expired' => 'Token expired',
    'token_invalid' => 'Invalid token',

    // Routes
    'route_not_found' => 'Route not found',
    'no_route_found' => 'No route found',
    'no_path_between_stations' => "No path exists between ':from' and ':to'",

    // Stations
    'departure_station_not_found' => 'Unknown departure station',
    'arrival_station_not_found' => 'Unknown arrival station',
    'station_not_exists' => "Station ':station' does not exist",

    // Field validation
    'from_station_required' => 'Departure station is required',
    'from_station_string' => 'Departure station must be a string',
    'from_station_max' => 'Departure station must not exceed :max characters',
    'to_station_required' => 'Arrival station is required',
    'to_station_string' => 'Arrival station must be a string',
    'to_station_max' => 'Arrival station must not exceed :max characters',
    'analytic_code_required' => 'Analytic code is required',
    'analytic_code_string' => 'Analytic code must be a string',
    'analytic_code_invalid' => 'Analytic code must be one of: :codes',

    // Generic errors
    'server_error' => 'Server error',
    'not_found' => 'Resource not found',
];
