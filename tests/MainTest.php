<?php

/**
 * Created by PhpStorm.
 * User: tebazil
 * Date: 29.08.15
 * Time: 20:26
 */
class MainTest extends \PHPUnit_Framework_TestCase {
    public function testDefaultResponses() {
        $this->newYiiConsoleApp();
        $this->attachBehavior();
        $generators = (new \tebazil\yii2\behaviors\vodka\VodkaBehavior())->generators;
        $this->assertTrue(is_array($generators));
        foreach($generators as $generator) {
            $this->assertTrue($generator instanceof \Closure);
            $this->assertTrue((bool)strlen($generator()));
        }
    }

    public function testWebAppWithVodka() {


        $this->newYiiWebApp();
        $this->attachBehavior();

            $output = $this->_testWebapp();


        $this->assertTrue((bool)strlen($output));
    }

    public function testWebAppWithVodkaWithCustomResponse() {
        $expectedOutput='babba';
        $this->newYiiWebApp();
        $this->attachBehavior(100, function() use ($expectedOutput) {
            return $expectedOutput;
        });

        $output = $this->_testWebapp();

        $this->assertTrue((bool)substr_count($output, $expectedOutput));
    }

    public function testWebappWithoutVodka()
    {
        $this->setExpectedException('yii\web\NotFoundHttpException');
        $this->newYiiWebApp();
        $this->attachBehavior(0);
        $this->_testWebapp();
    }

    public function testConsoleAppWithVodka()
    {
        $this->newYiiConsoleApp();
        $this->attachBehavior();
        $output = $this->_testConsoleapp();
        $this->assertTrue((bool)strlen($output));

    }

    public function testConsoleAppWithoutVodka()
    {
        $this->setExpectedException('yii\console\Exception');
        $this->newYiiConsoleApp();
        $this->attachBehavior(0);
        $this->_testConsoleapp();

    }

    private function _testWebapp()
    {
        Yii::$app->request->setQueryParams(['test-test', 'go', 'one'=>1]);

        ob_start();
        try {
            Yii::$app->run();
        }
        catch (\yii\base\ExitException $e) {}
        $output = ob_get_clean();
        return $output;
    }


    private function _testConsoleapp()
    {

        Yii::$app->request->setParams(['test-test', 'go', 'one'=>1]);
        ob_start();

        try {
            Yii::$app->run();
        }
    catch (\yii\base\ExitException $e) {}
        $output = ob_get_clean();

        return $output;
    }

    private function newYiiWebApp()
    {
        new \yii\web\Application([
            'id' => 'test_app',
            'basePath' => __DIR__,
        ]);

    }

    private function newYiiConsoleApp()
    {
        new \yii\console\Application([
            'id' => 'test_app',
            'basePath' => __DIR__,
        ]);
    }

    private function attachBehavior($probability=100, $customGenerator=null)
    {
        Yii::$app->attachBehavior('vodka', [
            'class'=>'tebazil\yii2\behaviors\vodka\VodkaBehavior',
            'probability'=>$probability,
            'generators'=>$customGenerator ? [$customGenerator]:null,
        ]);
    }



}
