<?php
// Don't redefine the functions if included multiple times.
use GeotCore\Upgrade\GeoUpgrades;

if ( ! function_exists( 'GeotCore\toArray' ) ) {
	require __DIR__ . '/GeotFunctions.php';
	require __DIR__ . '/functions.php';
	require __DIR__ . '/filters.php';
	require __DIR__ . '/global-functions.php';
	require __DIR__ . '/database.php';
}

// Upgrades
GeoUpgrades::init();