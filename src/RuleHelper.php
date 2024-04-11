<?php

namespace leandrogehlen\querybuilder;

class RuleHelper
{

    /**
     * Adds a table prefix for any field in conditions if not existing
     * @param array $rules<string, mixed> for Translator
     * @param string $prefix
     * @return array
     */
    public static function addPrefixToRules(array $rules, string $prefix) : array
    {
        if (!isset($rules['rules']) || !$rules['rules']) {
            return $rules;
        }

        $out = [];
        foreach ($rules['rules'] as $key => $rule) {
            if (isset($rule['condition'])) {
                $out[$key] = static::addPrefixToRules($rule, $prefix);
            } else {
                if(!str_contains($rule['field'], ".")) {
                    $rule['field'] = "$prefix.".$rule['field'];
                }
                $out[$key] = $rule;
            }
        }
        $rules['rules'] = $out;
        return $rules;

    }




}