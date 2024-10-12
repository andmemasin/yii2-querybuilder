<?php

namespace leandrogehlen\querybuilder;

use yii\helpers\ArrayHelper;

/**
 * Translator is used to build WHERE clauses from rules configuration
 *
 * The typical usage of Translator is as follows,
 *
 * ```php
 * public function actionIndex()
 * {
 *     $query = Customer::find();
 *     $rules = Yii::$app->request->post('rules');
 *
 *     if ($rules) {
 *         $translator = new Translator(Json::decode($rules));
 *         $query->andWhere($translator->where())
 *               ->addParams($translator->params());
 *     }
 *
 *     $dataProvider = new ActiveDataProvider([
 *         'query' => $query,
 *     ]);
 *
 *     return $this->render('index', [
 *         'dataProvider' => $dataProvider,
 *     ]);
 * }
 * ```
 * @author Leandro Gehlen <leandrogehlen@gmail.com>
 */
class Translator
{
    private string $_where;

    /** @var array<string, mixed> */
    private array $_params = [];

    /** @var array<string, mixed> */
    private array $_operators;

    /** @var string $paramPrefix prefix added to parameters, to be changed in case of multiple translator params being merged */
    private string $paramPrefix = "p";


    /**
     * Constructors.
     * @param array<mixed> $data Rules configuraion
     * @param ?string $paramPrefix prefix added to parameters, to be changed in case of multiple translator params being merged
     */
    public function __construct(private readonly array $data, ?string $paramPrefix = null)
    {
        $this->init();
        if(!is_null($paramPrefix)){
            $this->paramPrefix = $paramPrefix;
        }
        /** @var Rule $rules */
        $rules = \Yii::createObject(array_merge([
            'class' => Rule::class,
        ],$this->data));

        $this->_where = $this->buildWhere($rules);

    }


    /**
     * Encodes filter rule into SQL condition
     * @param string $field field name
     * @param string $type operator type
     * @param array<string, mixed> $params query parameters
     * @return string encoded rule
     */
    protected function encodeRule(string $field, string $type, array $params): string
    {
        $pattern = $this->_operators[$type];
        $keys = array_keys($params);

        if (is_string($pattern)) {
            $replacement = count($keys)>0 ? $keys[0] : null;
        } else {
            $op = ArrayHelper::getValue($pattern, 'op');
            $list = ArrayHelper::getValue($pattern, 'list');
            if (!is_null($list)){
                $sep = ArrayHelper::getValue($pattern, 'sep');
                $replacement = implode($sep, $keys);
            } else {
                $fn = ArrayHelper::getValue($pattern, 'fn');
                $replacement = key($params);
                $params[$replacement] = call_user_func($fn, $params[$replacement]);
            }
            $pattern = $op;
        }

        $this->_params = array_merge($this->_params, $params);
        return $field . " " . (is_null($replacement) ? $pattern : str_replace("?", $replacement, $pattern) );
    }


    /**
     * @return string the WHERE clause
     */
    protected function buildWhere(Rule $rule) : string
    {
        if(count($rule->children) === 0) {
            return '';
        }

        $where = [];
        $condition = " " . $rule->condition . " ";

        foreach ($rule->children as $child) {
            if (isset($child->condition)) {
                $where[] = $this->buildWhere($child);
            } else {
                $params = [];
                $operator = $child->operator;
                $field = $child->field;
                $value = ArrayHelper::getValue($child, 'value');

                if ($value !== null) {
                    $i = count($this->_params);
                    if (!is_array($value)) {
                        $value = [$value];
                    }

                    foreach ($value as $v) {
                        $params[":{$this->paramPrefix}_$i"] = $v;
                        $i++;
                    }
                }
                $where[] = $this->encodeRule($field, $operator, $params);
            }
        }
        return "(" . implode($condition, $where) . ")";
    }

    /**
     * Returns query WHERE condition.
     * @return string
     */
    public function where() : string
    {
        return $this->_where;
    }

    /**
     * Returns the parameters to be bound to the query.
     * @return array<string,mixed>
     */
    public function params() : array
    {
        return $this->_params;
    }



    /**
     * @inheritdoc
     */
    private function init() : void
    {
        $this->_operators = [
            'equal' =>            '= ?',
            'not_equal' =>        '<> ?',
            'in' =>               ['op' => 'IN (?)',     'list' => true, 'sep' => ', ' ],
            'not_in' =>           ['op' => 'NOT IN (?)', 'list' => true, 'sep' => ', '],
            'less' =>             '< ?',
            'less_or_equal' =>    '<= ?',
            'greater' =>          '> ?',
            'greater_or_equal' => '>= ?',
            'between' =>          ['op' => 'BETWEEN ?',   'list' => true, 'sep' => ' AND '],
            'not_between' =>      ['op' => 'NOT BETWEEN ?',   'list' => true, 'sep' => ' AND '],
            'begins_with' =>      ['op' => 'LIKE ?',     'fn' => function(string $value){ return "$value%"; } ],
            'not_begins_with' =>  ['op' => 'NOT LIKE ?', 'fn' => function(string $value){ return "$value%"; } ],
            'contains' =>         ['op' => 'LIKE ?',     'fn' => function(string $value){ return "%$value%"; } ],
            'not_contains' =>     ['op' => 'NOT LIKE ?', 'fn' => function(string $value){ return "%$value%"; } ],
            'ends_with' =>        ['op' => 'LIKE ?',     'fn' => function(string $value){ return "%$value"; } ],
            'not_ends_with' =>    ['op' => 'NOT LIKE ?', 'fn' => function(string $value){ return "%$value"; } ],
            'is_empty' =>         '= ""',
            'is_not_empty' =>     '<> ""',
            'is_null' =>          'IS NULL',
            'is_not_null' =>      'IS NOT NULL',
        ];
    }

}
