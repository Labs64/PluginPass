<?php
namespace PluginPass;

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
define( NS . 'PLUGIN_VERSION', '0.10.0' );

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
define( NS . 'PLUGIN_NAME', 'pluginpass' );

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
define( NS . 'PLUGIN_BASENAME', plugin_basename( __DIR__ ) );

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
define( NS . 'PLUGIN_TEXT_DOMAIN', 'pluginpass-pro-plugintheme-licensing' );

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Constants are namespace-prefixed via NS
define( NS . 'PLUGIN_MIN_PHP_VERSION', '8.2' );
