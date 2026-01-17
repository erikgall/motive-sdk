#!/bin/bash

# Motive SDK - Ralph Build Kickoff Script
# This script initializes the project structure for Ralph to build

set -e

echo "🚀 Initializing Motive SDK Project..."

# Navigate to project directory
cd ~/Code/motive-sdk

# Create directory structure
echo "📁 Creating directory structure..."
mkdir -p src/{Auth,Client,Contracts,Data/{Concerns},Enums,Exceptions,Facades,Http/Middleware,Pagination,Resources/{Concerns},Testing/Factories,Webhooks}
mkdir -p tests/{Unit/{Auth,Client,Contracts,Data,Enums,Exceptions,Pagination,Resources,Webhooks},Feature}
mkdir -p config

# Initialize composer.json
echo "📦 Initializing composer.json..."
composer require nesbot/carbon --quiet
composer require mockery/mockery --dev --quiet
composer require orchestra/testbench --dev --quiet

# Create phpunit.xml
echo "🧪 Creating phpunit.xml..."
cat > phpunit.xml << 'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         testdox="true"
         stopOnFailure="false">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
EOF

# Create phpstan.neon
echo "🔍 Creating phpstan.neon..."
cat > phpstan.neon << 'EOF'
parameters:
    level: 8
    paths:
        - src
        - tests
    ignoreErrors:
        - '#PHPDoc tag @var#'
EOF


## Testing

```bash
./vendor/bin/phpunit
```

## Development

This package is built using Test-Driven Development (TDD) following the red-green-refactor cycle.

See [RALPH_BUILD.md](RALPH_BUILD.md) for complete build instructions.
EOF

# Install dependencies
echo "⬇️  Installing Composer dependencies..."
composer install --quiet

echo ""
echo "✅ Project initialized successfully!"
echo ""
echo "📋 Next steps for Ralph:"
echo "1. Read RALPH_BUILD.md for comprehensive TDD instructions"
echo "2. Start with Phase 1: Foundation (Core Infrastructure)"
echo "3. Always write tests FIRST (RED phase)"
echo "4. Then write minimal implementation (GREEN phase)"
echo "5. Refactor for simplicity and clarity"
echo ""
echo "🧪 Run tests: ./vendor/bin/phpunit"
echo "✨ Fix style: ./vendor/bin/pint"
echo "🔍 Analyze: ./vendor/bin/phpstan analyse"
echo ""
echo "Happy building! 🚀"
