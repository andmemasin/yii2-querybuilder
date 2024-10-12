<?php

namespace leandrogehlen\querybuilder;

use yii\base\Component;

class Rule extends Component
{

    public bool $valid;
    public ?string $condition = null;

    public string|int $id;
    public string $field;
    public string $type;
    public string $input;
    public string $operator;
    public mixed $value = null;


    /** @var array<mixed> $rules raw input rules that we will make to child objects */
    public array $rules = [];
    /** @var self[] */
    public array $children = [];

    public function init() : void
    {
        parent::init();
        foreach ($this->rules as $ruleAttributes) {
            /** @var Rule $child */
            $child = \Yii::createObject(array_merge([
                'class' => self::class,
            ],$ruleAttributes));
            $this->children[] = $child;
        }
    }
}