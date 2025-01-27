<?php


namespace leandrogehlen\querybuilder\tests;

use leandrogehlen\querybuilder\Translator;
use yii\db\Query;

class TranslatorTest extends TestCase
{

    public static function rulesProvider()
    {
        return [
            [
                null, // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'equal', 'value' => 'joe'],
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'not_equal', 'value' => 'bruce'],
                ]],
                ['(name = :p_0 and name <> :p_1)', [':p_0' => 'joe', ':p_1' => 'bruce']],
            ],
            [
                'a', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'id', 'type' => 'integer', 'operator' => 'in', 'value' => [1,2,3]],
                    [ 'field' => 'id', 'type' => 'integer', 'operator' => 'not_in', 'value' => [4,5]],
                ]],
                ['(id IN (:a_0, :a_1, :a_2) and id NOT IN (:a_3, :a_4))', [':a_0'=>1, ':a_1'=>2, ':a_2'=>3, ':a_3'=>4, ':a_4'=>5]],
            ],
            [
                'b', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'id', 'type' => 'integer', 'operator' => 'less', 'value' => 100],
                    [ 'field' => 'id', 'type' => 'integer', 'operator' => 'less_or_equal', 'value' => 50],
                ]],
                ['(id < :b_0 and id <= :b_1)', [':b_0'=>100, ':b_1'=>50]],
            ],
            [
                'c', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'id', 'type' => 'integer', 'operator' => 'greater', 'value' => 10],
                    [ 'field' => 'id', 'type' => 'integer', 'operator' => 'greater_or_equal', 'value' => 20],
                ]],
                ['(id > :c_0 and id >= :c_1)', [':c_0'=>10, ':c_1'=>20]],
            ],
            [
                'd', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'date', 'type' => 'date', 'operator' => 'between', 'value' => ['2015-01-01','2015-01-30']],
                ]],
                ['(date BETWEEN :d_0 AND :d_1)', [':d_0'=>'2015-01-01', ':d_1'=>'2015-01-30']],
            ],
            [
                'e', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'date', 'type' => 'date', 'operator' => 'not_between', 'value' => ['2015-01-01','2015-01-30']],
                ]],
                ['(date NOT BETWEEN :e_0 AND :e_1)', [':e_0'=>'2015-01-01', ':e_1'=>'2015-01-30']],
            ],
            [
                'f', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'begins_with', 'value' => 'joe'],
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'not_begins_with', 'value' => 'bruce'],
                ]],
                ['(name LIKE :f_0 and name NOT LIKE :f_1)', [':f_0'=>'joe%', ':f_1'=> 'bruce%']],
            ],
            [
                'g', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'contains', 'value' => 'thomas'],
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'not_contains', 'value' => 'paul'],
                ]],
                ['(name LIKE :g_0 and name NOT LIKE :g_1)', [':g_0'=>'%thomas%', ':g_1'=> '%paul%']],
            ],
            [
                'h', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'ends_with', 'value' => 'brian'],
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'not_ends_with', 'value' => 'david'],
                ]],
                ['(name LIKE :h_0 and name NOT LIKE :h_1)', [':h_0'=>'%brian', ':h_1'=> '%david']],
            ],
            [
                'i', // paramPrefix
                ['condition' => "or", 'rules' => [
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'is_empty'],
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'is_not_empty'],
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'is_null'],
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'is_not_null'],
                ]],
                ['(name = "" or name <> "" or name IS NULL or name IS NOT NULL)', []],
            ],
            [
                'j', // paramPrefix
                ['condition' => "and", 'rules' => [
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'begins_with', 'value' => 'kurt'],
                    [ 'field' => 'name', 'type' => 'string', 'operator' => 'ends_with', 'value' => 'cobain'],
                    ['condition' => 'or', 'rules'=>[
                        [ 'field' => 'name', 'type' => 'string', 'operator' => 'equal', 'value' => 'joe'],
                        [ 'field' => 'name', 'type' => 'string', 'operator' => 'equal', 'value' => 'paul'],
                        ['condition' => 'and', 'rules'=>[
                            [ 'field' => 'id', 'type' => 'integer', 'operator' => 'equal', 'value' => 10],
                        ]],
                    ]],
                ]],
                ['(name LIKE :j_0 and name LIKE :j_1 and (name = :j_2 or name = :j_3 or (id = :j_4)))', [
                    ':j_0'=>'kurt%',':j_1' =>'%cobain', ':j_2' => 'joe', ':j_3' => 'paul', ':j_4' => 10,
                ]],
            ],

        ];
    }

    public static function mergedRulesProvider() : array
    {
        return [
            [
                [
                    [
                        'a', // paramPrefix
                        ['condition' => "and", 'rules' => [
                            [ 'field' => 'id', 'type' => 'integer', 'operator' => 'in', 'value' => [1,2,3]],
                            [ 'field' => 'id', 'type' => 'integer', 'operator' => 'not_in', 'value' => [4,5]],
                        ]],
                    ],
                    [
                        'b', // paramPrefix
                        ['condition' => "and", 'rules' => [
                            [ 'field' => 'id', 'type' => 'integer', 'operator' => 'less', 'value' => 100],
                            [ 'field' => 'id', 'type' => 'integer', 'operator' => 'less_or_equal', 'value' => 50],
                        ]],
                    ],
                ],
                // expected
                [['and',
                    '(id IN (:a_0, :a_1, :a_2) and id NOT IN (:a_3, :a_4))',
                    '(id < :b_0 and id <= :b_1)'
                ], [
                    ':a_0'=>1, ':a_1'=>2, ':a_2'=>3, ':a_3'=>4, ':a_4'=>5,
                    ':b_0'=>100, ':b_1'=>50,
                ]],
            ],
        ];
    }


    /**
     * @dataProvider rulesProvider
     */
    public function testRules(?string $paramPrefix, array $rule, array $expected)
    {
        $expectedWhere = $expected[0];
        $translator = new Translator($rule, $paramPrefix);
        $this->assertEquals($expectedWhere, $translator->where());
    }

    /**
     * @dataProvider rulesProvider
     */
    public function testHasParamValues(?string $paramPrefix, array $rule, array $expected) {
        $translator = new Translator($rule, $paramPrefix);
        $params = $translator->params();
        if(empty($expected[1])) {
            $this->assertEquals([], $params);
        }
        foreach ($expected[1] as $key => $value) {
            $values = array_values($params);
            $this->assertTrue(in_array($value,$values));
        }
    }

    /**
     * @dataProvider rulesProvider
     */
    public function testHasRightParamsCount(?string $paramPrefix, array $rule, array $expected) {
        $translator = new Translator($rule, $paramPrefix);
        $params = $translator->params();
        $this->assertEquals(count($params),count($expected[1]));
    }

    /**
     * @dataProvider mergedRulesProvider
     */
    public function testMergedRulesParams(array $mergedRules, array $expected)
    {
        $query = new Query();
        foreach ($mergedRules as $item) {
            $paramPrefix = $item[0];
            $rule = $item[1];
            $translator = new Translator($rule, $paramPrefix);
            $query->andWhere($translator->where());
            $query->addParams($translator->params());
        }
        $params = $query->params;
        $expectedWhere = $expected[0];
        $expectedParams = $expected[1];
        $this->assertEquals($expectedParams, $params);
        $this->assertEquals($expectedParams, $params);

    }
    /**
     * @dataProvider mergedRulesProvider
     */
    public function testMergedRulesWhere(array $mergedRules, array $expected)
    {
        $query = new Query();
        foreach ($mergedRules as $item) {
            $paramPrefix = $item[0];
            $rule = $item[1];
            $translator = new Translator($rule, $paramPrefix);
            $query->andWhere($translator->where());
            $query->addParams($translator->params());
        }
        $expectedWhere = $expected[0];
        $this->assertEquals($expectedWhere, $query->where);
    }
}
