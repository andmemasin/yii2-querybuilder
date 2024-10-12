<?php

namespace leandrogehlen\querybuilder;

use yii\base\Component;

class Rule extends Component
{

    public string $condition;
    public string $operator;
    public string $field;
    public mixed $value = null;
    public bool $valid;
    public string|int $id;
    public string $type;
    public string $input;

    /** @var array<mixed> */
    public array $rules = [];
    /** @var self[] */
    public array $children = [];

    public function init()
    {
        parent::init();
        foreach ($this->rules as $ruleAttributes) {
            $this->children[] = \Yii::createObject(array_merge([
                'class' => self::class,
            ],$ruleAttributes));
        }
    }
}