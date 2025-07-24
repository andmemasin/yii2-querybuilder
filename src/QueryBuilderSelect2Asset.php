<?php

namespace leandrogehlen\querybuilder;

use yii\web\AssetBundle;

/**
 * Asset bundle for jQuery QueryBuilder Select2 Filters Plugin
 * Provides searchable field/column selector using Select2
 *
 * @author Auto-generated
 */
class QueryBuilderSelect2Asset extends AssetBundle
{
    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }

    /**
     * @var string[]
     */
    public $js = [
        'select2-filters.js',
    ];

    /**
     * @var string[]
     */
    public $css = [
        'select2-filters.css',
    ];

    /**
     * @var array<class-string>
     */
    public $depends = [
        'leandrogehlen\querybuilder\QueryBuilderAsset',
        'kartik\select2\Select2Asset',
    ];
}