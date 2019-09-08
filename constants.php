<?php
namespace PluginPass;

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

define( NS . 'PLUGIN_VERSION', '0.9.10' );

define( NS . 'PLUGIN_NAME', 'pluginpass' );

define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __DIR__ ) );

define( NS . 'PLUGIN_TEXT_DOMAIN', 'pluginpass' );

define( NS . 'PLUGIN_MIN_PHP_VERSION', '5.6.0' );
