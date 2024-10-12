<?php

namespace leandrogehlen\querybuilder;

use yii\base\Widget;

/**
 * QueryBuilder renders a jQuery QueryBuilder component.
 *
 * @see http://mistic100.github.io/jQuery-QueryBuilder/
 * @author Leandro Gehlen <leandrogehlen@gmail.com>
 */
class QueryBuilder extends Widget {

    /**
     * @inheridoc
     */
    public string $pluginName = 'queryBuilder';

    /**
     * @return string[]
     * @inheritdoc
     */
    protected function assets() : array
    {
        return [
            QueryBuilderAsset::class
        ];
    }

}
