<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Messages API - Français
    |--------------------------------------------------------------------------
    */

    // Validation générale
    'validation_error' => 'Erreur de validation',
    'invalid_request' => 'Requête invalide',

    // Authentification
    'invalid_credentials' => 'Identifiants invalides',
    'logout_success' => 'Déconnexion réussie',
    'unauthorized' => 'Non autorisé',
    'token_expired' => 'Token expiré',
    'token_invalid' => 'Token invalide',

    // Routes / Trajets
    'route_not_found' => 'Trajet non trouvé',
    'no_route_found' => 'Aucun itinéraire trouvé',
    'no_path_between_stations' => "Aucun chemin n'existe entre ':from' et ':to'",

    // Stations
    'departure_station_not_found' => 'Station de départ inconnue',
    'arrival_station_not_found' => "Station d'arrivée inconnue",
    'station_not_exists' => "La station ':station' n'existe pas",

    // Validation des champs
    'from_station_required' => 'La station de départ est obligatoire',
    'from_station_string' => 'La station de départ doit être une chaîne de caractères',
    'from_station_max' => 'La station de départ ne doit pas dépasser :max caractères',
    'to_station_required' => "La station d'arrivée est obligatoire",
    'to_station_string' => "La station d'arrivée doit être une chaîne de caractères",
    'to_station_max' => "La station d'arrivée ne doit pas dépasser :max caractères",
    'analytic_code_required' => 'Le code analytique est obligatoire',
    'analytic_code_string' => 'Le code analytique doit être une chaîne de caractères',
    'analytic_code_invalid' => 'Le code analytique doit être l\'un des suivants : :codes',

    // Erreurs génériques
    'server_error' => 'Erreur serveur',
    'not_found' => 'Ressource non trouvée',
];
