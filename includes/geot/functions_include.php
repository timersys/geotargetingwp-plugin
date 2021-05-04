<?php
// Don't redefine the functions if included multiple times.
use GeotCore\Upgrade\GeoUpgrades;

if ( ! function_exists( 'GeotCore\toArray' ) ) {
	require __DIR__ . '/GeotCore.php';
	require __DIR__ . '/functions.php';
	require __DIR__ . '/filters.php';
	require __DIR__ . '/global-functions.php';
}

// Upgrades
GeoUpgrades::init();

// We now supress the session in the session file, so load core functions files on load
\GeotCore\GeotCore::instance();
