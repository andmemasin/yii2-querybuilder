<?php

namespace leandrogehlen\querybuilder;

/**
 * QueryBuilder renders a jQuery QueryBuilder component.
 *
 * @see http://mistic100.github.io/jQuery-QueryBuilder/
 * @author Leandro Gehlen <leandrogehlen@gmail.com>
 */
class QueryBuilder extends \soluto\plugin\Widget {

    /**
     * @inheridoc
     */
    public $pluginName = 'queryBuilder';

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
