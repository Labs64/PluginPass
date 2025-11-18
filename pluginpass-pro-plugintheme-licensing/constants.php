<?php
namespace PluginPass;

/**
 * Define Constants
 */

if ( ! defined( __NAMESPACE__ . '\NS' ) ) {
	define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );
}

if ( ! defined( NS . 'PLUGIN_VERSION' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
	define( NS . 'PLUGIN_VERSION', '0.10.2' );
}

if ( ! defined( NS . 'PLUGIN_NAME' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
	define( NS . 'PLUGIN_NAME', 'pluginpass' );
}

if ( ! defined( NS . 'PLUGIN_NAME_DIR' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
	define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( NS . 'PLUGIN_NAME_URL' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
	define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( NS . 'PLUGIN_BASENAME' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
	define( NS . 'PLUGIN_BASENAME', plugin_basename( __DIR__ ) );
}

if ( ! defined( NS . 'PLUGIN_TEXT_DOMAIN' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
	define( NS . 'PLUGIN_TEXT_DOMAIN', 'pluginpass-pro-plugintheme-licensing' );
}

if ( ! defined( NS . 'PLUGIN_MIN_PHP_VERSION' ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
	define( NS . 'PLUGIN_MIN_PHP_VERSION', '8.2' );
}
