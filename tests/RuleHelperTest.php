<?php

namespace leandrogehlen\querybuilder\tests;

use leandrogehlen\querybuilder\Rule;
use leandrogehlen\querybuilder\RuleHelper;
use leandrogehlen\querybuilder\Translator;

class RuleHelperTest extends TestCase
{


    /**
     * @dataProvider rulesProvider
     */
    public function testPrefixesGetAdded(?string $paramPrefix,array $ruleData, array $expected1,?string $tablePrefix, string $expected)
    {
        /** @var RuleHelper $helper */
        $helper = \Yii::createObject(RuleHelper::class);

        /** @var Rule $rules */
        $rules = \Yii::createObject(array_merge([
            'class' => Rule::class,
        ],$ruleData));

        $rules = $helper->addPrefixToRules($rules, $tablePrefix);
        /** @var Translator $translator */
        $translator = \Yii::createObject(Translator::class, [$rules, $paramPrefix]);
        $this->assertEquals($expected, $translator->where());
    }


    public static function rulesProvider()
    {
        $baseData = TranslatorTest::rulesProvider();
        $newData = [];
        $addParams = [
            [
                'myTableName',
                '(myTableName.name = :p_0 and myTableName.name <> :p_1)',
            ],
            [
                'table2',
                '(table2.id IN (:a_0, :a_1, :a_2) and table2.id NOT IN (:a_3, :a_4))',
            ],
            [
                'table3',
                '(table3.id < :b_0 and table3.id <= :b_1)',
            ],
            [
                'table4',
                '(table4.id > :c_0 and table4.id >= :c_1)',
            ],
            [
                'table5',
                '(table5.date BETWEEN :d_0 AND :d_1)',
            ],
            [
                'table6',
                '(table6.date NOT BETWEEN :e_0 AND :e_1)',
            ],
            [
                'table7',
                '(table7.name LIKE :f_0 and table7.name NOT LIKE :f_1)',
            ],
            [
                'table8',
                '(table8.name LIKE :g_0 and table8.name NOT LIKE :g_1)',
            ],
            [
                'table9',
                '(table9.name LIKE :h_0 and table9.name NOT LIKE :h_1)',
            ],

        ];
        foreach ($addParams as $key => $item) {
            $newData[$key] = $baseData[$key];
            $newData[$key][] = $item[0];
            $newData[$key][] = $item[1];
        }
        return $newData;
    }

}