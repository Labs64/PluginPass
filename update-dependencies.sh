#!/bin/bash
# PluginPass - Composer Dependencies Update Script

set -e

echo "=================================="
echo "PluginPass Dependency Update"
echo "=================================="
echo ""

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed!"
    echo ""
    echo "Please install Composer first:"
    echo "  macOS: brew install composer"
    echo "  Linux: curl -sS https://getcomposer.org/installer | php"
    echo "  Windows: Download from https://getcomposer.org/download/"
    echo ""
    exit 1
fi

echo "✓ Composer found: $(composer --version)"
echo ""

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✓ PHP Version: $PHP_VERSION"

# Verify PHP version meets minimum requirement
if php -r "exit(version_compare(PHP_VERSION, '7.4.0', '>=') ? 0 : 1);"; then
    echo "✓ PHP version is compatible (>= 7.4)"
else
    echo "❌ PHP version is too old. Minimum required: 7.4"
    exit 1
fi

echo ""
echo "Updating Composer dependencies..."
echo ""

# Update dependencies
composer update --no-dev --optimize-autoloader

echo ""
echo "=================================="
echo "Update Complete!"
echo "=================================="
echo ""
echo "Dependencies updated:"
composer show --no-dev | grep -E "labs64|php-curl"
echo ""
echo "Next steps:"
echo "1. Test the plugin in a development environment"
echo "2. Verify license validation works correctly"
echo "3. Check for any PHP errors in debug log"
echo "4. Review the admin interface for any issues"
echo ""
