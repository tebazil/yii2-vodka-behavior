# Vodka. Your application talks to you (tm) 

Vodka is a joke "drunk" application behavior that intercepts the response at a given probability (default is 0.75) and replaces it with some "quote" from the default or custom generators. Works for both web and console application.  
Here is the output example:
```
VODKA BEHAVIOR says:
Obna obbbaaoo n  bnbnb oabb bnn 
a bn  a anba baaooaobboboaabna n  nboo oan b a   oa nbonbao ona b
  obnob b nob bb  nnnb  boaban b n o anon oaoannan oaaooaaababobo
anonob a anooaao nb aa ooo bb  n bnnabab abbbbno bnb nnoboo noano
ooanabaaoonn anaao ab n an  ona  n o  anaaa nbobbann  anoo boa ob
banaooa    noanaonbbno o oono  a .
```

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require tebazil/yii2-vodka-behavior
```

or add

```
"tebazil/yii2-vodka-behavior": "*"
```

to the `require` section of your `composer.json` file.

## Basic usage

VodkaBehavior is a typical yii2 behavior, that can be attached to the application. The most simple way is to attach the behavior in application's config section:

```php
return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    ...
	'as vodka'=> [
		'class'=>'tebazil\yii2\behaviors\vodka\VodkaBehavior',
	],
	...
	'components'=>[
	...
	],
	...

```

There. Now when you call your application (even with non existent routes), "quotes" will appear in browser/console at a given probability.

## Advanced usage
You may also benefit from some additional configuration. You can configure `generators`, `probability`, and `phrase` to further customize a drunk behavior.  
`phrase` (string) - is a prefix phrase to generator's output. The default value is `VODKA BEHAVIOR says:`  
`probability` (float) - is a probability from 0 to 1 at which the output will be intercepted. 0 means behavior never fires. 1 means it fires all the time. The default value is `0.75`.
`generators` is the array of anonymous functions that generate the output. When you define this parameter, the default generators are replaced with the ones you specified. The anonymous function takes no parameters and returns output. For universal usage, make sure you return string from a generator. The default value of `generators` is array of two bundled generators.  

Here is the example of full configuration:  

```php
return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    ...
	'as vodka'=> [
		'class'=>'tebazil\yii2\behaviors\vodka\VodkaBehavior',
		'probability'=>0.95,
		'phrase'=>
		    'Hi, I am your application ... Intrigued, huh? Here is what I have to say:',
		'generators'=>[
		function() {
		  return 'I think everything is possible. 
		  You just have to listen to your heart and work hard.';
		},
		function() {
		    $coreDevs = ['Qiang','Cebe','Samdark','Will Smith'];
		    $endingPhrases = [
		        'The resemblance is unique.',
		        'Sometimes they drink tea together.',
		        'But only in the dark.',
		    ];
		    $randMember = function($arr) { 
		        return $arr[array_rand($arr)]; 
		    };
		    return 
		        $randMember($coreDevs).' looks like '.
		        $randMember($coreDevs).'. '.
		        $randMember($endingPhrases);
		}
		]
	],
	...
	'components'=>[
	...
	],
	...

```

## How it works
When `Application::EVENT_BEFORE_REQUEST` happens, the probability is being calculated. If, by probability, it is not time to run - nothing happens. If it is time to run:
 * In case of `WebApplication`, the `data` attribute of response is set, then the response is sent.
 * In case of `ConsoleApplication` the output is echoed, then the the application execution ends.
 
## Advanced generator usage
You can return objects and arrays in generators. In case of `ConsoleApplication`, they will be displayed via `print_r`. In case of `WebApplication` the result would depend on what format you are trying to output generator's result in. For example, you might be getting error with array and object without __toString() implementation in case of `html` format.