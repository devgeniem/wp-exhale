<?php
/**
 * Plugin Name: WP Exhale
 * Description: Developer friendly exporter for WordPress
 * Version: 0.1.5
 * Author: Onni Hakala / Geniem Oy
 * Author URI: http://geniem.com
 * License: GPLv2
 */

namespace Exhale;

// If this was installed directly load composer packages from this folder
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require __DIR__ . '/vendor/autoload.php';
}

// Load modules
require_once('classes/Core.php');
require_once('classes/Type/XML.php');

// Load Helpers
require_once('classes/Base/XML.php');
require_once('classes/Base/XML_Item.php');

// Load default presets for known services
require_once('classes/Vendor/Vuokraovi.php');
require_once('classes/Vendor/Oikotie.php');

// Apply hooks and filters
Core::init();
