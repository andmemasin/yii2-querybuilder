<?php

namespace leandrogehlen\querybuilder;

class RuleHelper
{

    /**
     * Adds a table prefix for any field in conditions if not existing
     */
    public static function addPrefixToRules(Rule $rules, string $prefix) : Rule
    {
        if(count($rules->children) === 0) {
            return $rules;
        }

        foreach ($rules->children as $key => $child) {
            if ($child->condition !== null) {
                $rules->children[$key] = static::addPrefixToRules($child, $prefix);
            } else {
                if(!is_null($child->field) && !str_contains($child->field, ".")) {
                    $child->field = "$prefix.".$child->field;
                }
            }
        }
        return $rules;

    }


    /**
     * Removes a specific table prefix from any field in conditions
     */
    public static function removePrefixFromRules(Rule $rules, string $prefix) : Rule
    {
        if(count($rules->children) === 0) {
            return $rules;
        }

        foreach ($rules->children as $key => $child) {
            if ($child->condition !== null) {
                $rules->children[$key] = static::removePrefixFromRules($child, $prefix);
            } else {
                if(!is_null($child->field) && str_contains($child->field, "$prefix.")) {
                    $child->field = str_replace("$prefix.", "", $child->field);
                }
            }
        }
        return $rules;
    }


}