# php-friendly
A utility for adding non-native class friendship capabilities to PHP applications

## Overview
The friend classes [RFC](https://wiki.php.net/rfc/friend-classes) suggested that friend classes would be php-native feature. Since it was declided we've decided to develop our own non-native solution that solves the same problem - how to make multiple classes under the same namespace "friends", and let them call each-other's protected methods.

## Requirements
* PHP >= 7.3
* php-unit >= 8.3

## Composer Install
Add the dependency myheritage/php-friendly to your project if you use Composer to manage the dependencies of your project.
```
$ composer require myheritage/php-friendly
```

## Usage example
### Callee
Shared / Exposed functions must be annotated with the `@friendly` annotation and have the caller as the last parameter to the function
```
<?php
namespace MyHeritage\Friends\Tests\Friendly;

use MyHeritage\Friends\Friendly;

class AFriendlyClass
{
    use Friendly;

    /**
     * @friendly
     * @param $message
     * @param $caller
     *
     * @return mixed
     */
    protected function friendlyMethod($message, $caller = null)
    {
        return $message;
    }

    protected function notFriendly()
    {
        return "Booo";
    }
}
```

### Caller
Note that the caller must pass itself (`$this`) as a parameter to the callee function.
```
<?php
namespace MyHeritage\Friends\Tests\Friendly;

class AnotherFriendlyClass
{
    public function callMe()
    {
        echo (new AFriendlyClass())->friendlyMethod('hello', $this);
    }
}
```
