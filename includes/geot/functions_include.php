<?php
// Don't redefine the functions if included multiple times.
use GeotCore\Session\GeotSession;
use GeotCore\Upgrade\GeoUpgrades;

if ( ! function_exists( 'GeotCore\toArray' ) ) {
	require __DIR__ . '/functions.php';
	require __DIR__ . '/filters.php';
	require __DIR__ . '/global-functions.php';
	require __DIR__ . '/database.php';
	require __DIR__ . '/plugins.php';
}

// Init the session class on file load
GeotSession::instance();

// Upgrades
GeoUpgrades::init();