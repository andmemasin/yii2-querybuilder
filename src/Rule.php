<?php

namespace leandrogehlen\querybuilder;


class Rule
{

    public ?bool $valid;
    public ?string $condition = null;

    public null|string|int $id;
    public ?string $field;
    public ?string $type;
    public ?string $input;
    public ?string $operator;
    public mixed $value = null;


    /** @var array<mixed> $rules raw input rules that we will make to child objects */
    public array $rules = [];
    /** @var self[] */
    public array $children = [];

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->valid = $attributes['valid'] ?? null;
        $this->condition = $attributes['condition'] ?? null;

        $this->rules = $attributes['rules'] ?? [];


        $this->id = $attributes['id'] ?? null;
        $this->field = $attributes['field'] ?? null;
        $this->type = $attributes['type'] ?? null;
        $this->input = $attributes['input'] ?? null;
        $this->operator = $attributes['operator'] ?? null;
        $this->value = $attributes['value'] ?? null;

        foreach ($this->rules as $rule) {
            $this->children[] = new Rule($rule);
        }
    }
}