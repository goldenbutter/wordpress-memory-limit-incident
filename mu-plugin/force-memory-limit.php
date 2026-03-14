<?php
/**
 * Plugin Name: Force Memory Limit
 * Description: Forces PHP memory limit to 512M at multiple stages of WordPress loading.
 * Author: Bithun Chatterjee
 * Version: 1.0.0
 */

/**
 * Set memory limit immediately when the file loads.
 * MU-plugins load before all other plugins, so this ensures
 * the memory limit is applied as early as possible.
 */
ini_set('memory_limit', '512M');

/**
 * Callback function to re-apply the memory limit.
 * Some plugins or PHP handlers override memory_limit later,
 * so we hook this function into multiple stages of WP bootstrap.
 */
$force_memory = function() {
    ini_set('memory_limit', '512M');
};

/**
 * I use priority 99999 to ensure our memory override runs LAST.
 * This guarantees that even if other plugins try to lower the memory limit,
 * our value (512M) will overwrite theirs.
 */
add_action('muplugins_loaded', $force_memory, 99999);
add_action('plugins_loaded', $force_memory, 99999);
add_action('after_setup_theme', $force_memory, 99999);
add_action('init', $force_memory, 99999);
add_action('wp_loaded', $force_memory, 99999);