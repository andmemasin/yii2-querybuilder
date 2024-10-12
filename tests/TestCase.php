<?php

namespace leandrogehlen\querybuilder\tests;

use yii\base\NotSupportedException;
use Yii;

/**
 * This is the base class for all unit tests.
 */
class TestCase extends  \PHPUnit\Framework\TestCase
{

    /**
     * Populates Yii::$app with a new application
     */
    protected function mockApplication()
    {
        static $config = [
            'id' => 'querybuilder-test',
            'basePath' => __DIR__,
        ];
        $config['vendorPath'] = dirname(dirname(__DIR__)) . '/vendor';
        new \yii\console\Application($config);
    }

    /**
     * Sets up before test
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->mockApplication();
    }

    /**
     * Clean up after test.
     * The application created with [[mockApplication]] will be destroyed.
     */
    protected function tearDown() : void
    {
        parent::tearDown();
        Yii::$app = null;
    }
}
