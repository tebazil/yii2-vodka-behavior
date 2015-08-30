<?php
namespace tebazil\yii2\behaviors\vodka;

use yii\base\Application;

class VodkaBehavior extends \yii\base\Behavior
{
    public $phrase = "VODKA BEHAVIOR says:";
    public $generators;
    public $probability = 0.75;

    public function init()
    {
        if (is_null($this->generators)) {
            $this->generators = $this->defaultGenerators();
        }
        parent::init();
    }

    public function events()
    {
        return [
            Application::EVENT_BEFORE_REQUEST => function () {
                if (rand(1, 100) / 100 > $this->probability)
                    return;
                $response = \Yii::$app->response;
                $content = $this->getModifiedResponseContent();
                $app = \Yii::$app;
                if ($app instanceof \yii\console\Application) {
                    echo PHP_EOL . (is_array($content) || is_object($content)) ? print_r($content,true):$content . PHP_EOL;
                    $app->end();
                } elseif ($app instanceof \yii\web\Application) {
                    $response->data = (is_array($content) || is_object($content)) ? $content : '<pre>' . $content . '</pre>';
                    $response->send();
                    $app->end();
                }

            }];
    }

    private function getModifiedResponseContent()
    {
        $generators = $this->generators;
        $generator = $generators[array_rand($generators)];
        $content = $generator();
        return (is_array($content) || is_object($content)) ? $content: $this->phrase . PHP_EOL . $content;
    }

    private function defaultGenerators()
    {
        return [
            function () {
                $generateRandomString = function() {
                    $symbols = ['a','b','n','o', ' '];
                    $length = 32;
                    $out = '';
                    for($i=0;$i<$length; $i++) {
                        $out.=$symbols[array_rand($symbols)];
                    }
                    return $out;
                };
                $content = '';
                for ($i = 0; $i < 10; $i++) {
                    $content .= $generateRandomString().(!($i%2) ? PHP_EOL:' ');
                }
                return ucfirst($content).'.';
            },
            function () {
                $content = print_r((\Yii::$app instanceof \yii\console\Application) ? \Yii::$app->request->getParams() : \Yii::$app->request->getQueryParams(), true);
                return $content;
            },
        ];
    }

}