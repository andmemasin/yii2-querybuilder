<?php

namespace leandrogehlen\querybuilder;

use PascalDeVink\ShortUuid\ShortUuid;
use Ramsey\Uuid\Uuid;
use yii\base\BaseObject;
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
 *         $translator = new Translator(Json::decode($rules),['currentParams'=>$query->params]);
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
class Translator extends BaseObject
{
    private string $_where;

    /** @var array<string, mixed> */
    private array $_params = [];

    /** @var array<string, mixed> */
    private array $_operators;

    /**
     * @var array<string, mixed> The params from yii\db\Query object that are already set so we don't overwrite them
     * @deprecated
     */
    private array $currentParams = [];

    /**
     * Constructors.
     * @param array<mixed> $data Rules configuraion
     * @param array<string, mixed> $config the configuration array to be applied to this object.
     */
    public function __construct($data, $config = [])
    {
        if(isset($config['currentParams'])){
            $this->setCurrentParams($config['currentParams']);
                    
        }
        parent::__construct($config);
        $this->_where = $this->buildWhere($data);
    }

    /**
     * @inheritdoc
     */
    public function init() : void
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
            'begins_with' =>      ['op' => 'LIKE ?',     'fn' => function($value){ return "$value%"; } ],
            'not_begins_with' =>  ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "$value%"; } ],
            'contains' =>         ['op' => 'LIKE ?',     'fn' => function($value){ return "%$value%"; } ],
            'not_contains' =>     ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "%$value%"; } ],
            'ends_with' =>        ['op' => 'LIKE ?',     'fn' => function($value){ return "%$value"; } ],
            'not_ends_with' =>    ['op' => 'NOT LIKE ?', 'fn' => function($value){ return "%$value"; } ],
            'is_empty' =>         '= ""',
            'is_not_empty' =>     '<> ""',
            'is_null' =>          'IS NULL',
            'is_not_null' =>      'IS NOT NULL'
        ];
    }


    /**
     * Encodes filter rule into SQL condition
     * @param string $field field name
     * @param string $type operator type
     * @param array<string, mixed> $params query parameters
     * @return string encoded rule
     */
    protected function encodeRule(string $field, string $type, array $params)
    {
        $pattern = $this->_operators[$type];
        $keys = array_keys($params);

        if (is_string($pattern)) {
            $replacement = !empty($keys) ? $keys[0] : null;
        } else {
            $op = ArrayHelper::getValue($pattern, 'op');
            $list = ArrayHelper::getValue($pattern, 'list');
            if ($list){
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
        return $field . " " . ($replacement ? str_replace("?", $replacement, $pattern) : $pattern);
    }

    /**
     * @param array<mixed> $data rules configuration
     * @return string the WHERE clause
     */
    protected function buildWhere($data)
    {
        if (!isset($data['rules']) || !$data['rules']) {
            return '';
        }

        $where = [];
        $condition = " " . $data['condition'] . " ";

        foreach ($data['rules'] as $rule) {
            if (isset($rule['condition'])) {
                $where[] = $this->buildWhere($rule);
            } else {
                $params = [];
                $operator = $rule['operator'];
                $field = $rule['field'];
                $value = ArrayHelper::getValue($rule, 'value');

                if ($value !== null) {

                    if (!is_array($value)) {
                        $value = [$value];
                    }

                    foreach ($value as $v) {
                        $params[":".$this->getNewParamName()] = $v;
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
    public function where()
    {
        return $this->_where;
    }

    /**
     * Returns the parameters to be bound to the query.
     * @return array<string,mixed>
     */
    public function params()
    {
        return array_merge($this->currentParams, $this->_params);
    }
    
    /**
     * Get a param name that should not conflict with any params already set
     * @return string
     */
    private function getNewParamName(){
        $shortUUid = new ShortUuid();
        return $shortUUid->encode(Uuid::uuid4());
    }

    /**
     *
     * @param array<string, mixed> $currentParams
     * @deprecated
     */
    public function setCurrentParams($currentParams) : void {
        $this->currentParams = $currentParams;
    }


} 
