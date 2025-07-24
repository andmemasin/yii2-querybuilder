# QueryBuilder Select2 Plugin

This plugin enhances the Yii2 QueryBuilder widget by making field selector dropdowns searchable using Select2. This is particularly useful when dealing with many fields (30+) where users need to type and filter field names.

## Installation

1. Ensure your application has Select2 installed (most Yii2 apps with Bootstrap already include this)
2. Include the plugin files in your widget directory:
   - `src/QueryBuilderSelect2Asset.php` - Asset bundle
   - `src/assets/select2-filters.js` - JavaScript enhancement
   - `src/assets/select2-filters.css` - Styling

## Usage

### Basic Implementation

In your view file, register the Select2 asset before using QueryBuilderForm:

```php
<?php
use leandrogehlen\querybuilder\QueryBuilderForm;
use leandrogehlen\querybuilder\QueryBuilderSelect2Asset;

// Register the Select2 plugin asset
QueryBuilderSelect2Asset::register($this);
?>

<?php QueryBuilderForm::begin([
    'rules' => $rules,
    'builder' => [
        'id' => 'query-builder',
        'pluginOptions' => [
            'filters' => [
                ['id' => 'name', 'label' => 'Full Name', 'type' => 'string'],
                ['id' => 'email', 'label' => 'Email Address', 'type' => 'string'],
                // ... many more fields
            ]
        ]
    ]
]) ?>
<?php QueryBuilderForm::end() ?>
```

### Controller Setup

Ensure your controller handles rules properly:

```php
public function actionIndex()
{
    $rulesParam = Yii::$app->request->get('rules');
    $rules = [];
    
    if ($rulesParam) {
        $decoded = Json::decode($rulesParam);
        if (is_array($decoded)) {
            $rules = $decoded;
        }
    }
    
    return $this->render('index', [
        'rules' => $rules,
    ]);
}
```

## Features

- **Searchable Dropdowns**: Type to filter field names
- **Bootstrap Theme**: Matches your existing Bootstrap styling
- **Auto-width**: Adapts to content width with reasonable minimum
- **Dynamic Rules**: Works with dynamically added/removed rules
- **Preserved Styling**: Maintains original QueryBuilder appearance

## Technical Details

### How It Works

1. The JavaScript plugin automatically detects QueryBuilder instances
2. Converts field selector dropdowns to Select2 with search capability
3. Uses MutationObserver to handle dynamically added rules
4. Preserves all original QueryBuilder functionality

### CSS Customization

The plugin includes responsive styling that:
- Sets minimum width of 160px for closed dropdowns
- Auto-sizes based on content
- Matches Bootstrap form styling
- Preserves original QueryBuilder red container styling

### JavaScript Events

The plugin hooks into QueryBuilder events:
- `afterCreateRuleFilters` - Enhances new rule dropdowns
- `afterAddRule` - Applies Select2 to newly added rules
- `beforeDeleteRule` - Cleans up Select2 instances

## Compatibility

- Requires jQuery QueryBuilder 2.5+
- Requires Select2 4.0+
- Compatible with Bootstrap 3/4
- Works with Yii2 2.0+

## Troubleshooting

### Select2 Not Loading
Ensure Select2 is available in your application. Check browser console for errors.

### Styling Issues
The plugin CSS should load after QueryBuilder CSS. Verify asset dependencies.

### Multiple QueryBuilders
The plugin automatically excludes elements with ID `query-builder-original` to avoid conflicts when comparing implementations.