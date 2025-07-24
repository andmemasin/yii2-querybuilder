# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Development Commands

```bash
# Install dependencies
composer install --prefer-dist --no-progress
yarn install

# Run tests
vendor/bin/phpunit

# Run static analysis
vendor/bin/phpstan analyse

# Validate configuration
composer validate --strict
```

## Architecture Overview

This is a Yii2 extension providing a jQuery QueryBuilder widget for building database queries visually. The architecture follows a clean separation of concerns:

### Core Components

- **`QueryBuilder.php`** - Main widget extending `soluto\plugin\Widget`
- **`QueryBuilderForm.php`** - Form wrapper widget for complete form integration
- **`Translator.php`** - Core business logic converting jQuery QueryBuilder rules to SQL WHERE clauses
- **`Rule.php`** - Data structure representing query rules (supports hierarchical AND/OR conditions)
- **`RuleHelper.php`** - Utilities for rule manipulation (add/remove table prefixes)

### Data Flow

1. View renders `QueryBuilderForm` with filter configuration
2. User builds query visually, rules submitted as JSON
3. Controller uses `Translator` to convert rules to SQL WHERE clause + parameters
4. Apply to ActiveQuery for database execution

### Key Features

- **SQL Translation**: 19 operators supported (equal, not_equal, in, like, between, etc.)
- **Parameter Binding**: Secure SQL injection prevention
- **Hierarchical Rules**: Nested AND/OR conditions
- **Multi-table Support**: Table prefix utilities via `RuleHelper`

## Testing

- PHPUnit-based with custom `TestCase` base class
- Mock Yii application setup for isolated testing
- PHPStan level 8 static analysis with strict rules
- PHP 8.0+ required, supports 8.0-8.3

## Dependencies

- Frontend: jQuery QueryBuilder v3.0.0+ (via NPM @npm alias)
- PHP: `solutosoft/yii2-plugin`, `yiisoft/arrays`
- Requires Bootstrap CSS integration in AppAsset