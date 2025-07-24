<?php

namespace leandrogehlen\querybuilder;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the [jquery QueryBuilder library](https://github.com/mistic100/jQuery-QueryBuilder)
 *
 * @author Leandro Gehlen <leandrogehlen@gmail.com>
 */
class QueryBuilderAsset extends AssetBundle
{

    public $sourcePath = '@npm/jQuery-QueryBuilder/dist';

    /**
     * @var string[]
     */
    public $js = [
        'js/query-builder.standalone.js',
    ];

    /**
     * @var string[]
     */
    public $css = [
        'css/query-builder.default.css',
    ];

    /**
     * @var array<class-string>
     */
    public $depends = [
        'yii\web\JqueryAsset'
    ];

}
