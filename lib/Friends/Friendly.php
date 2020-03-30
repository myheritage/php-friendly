<?php
namespace MyHeritage\Friends;

use Exception;
use ReflectionClass;
use ReflectionMethod;

/**
 * Use this trait to enable making protected methods friends with classes of the same/nested namespace
 * In order to make "friendly" methods you will need to:
 * 1. use this trait
 * 2. Annotate protected methods that you want to expose with @friendly annotation
 * 3. Add $caller optional parameter for the sake of better phpdoc
 *
 * A friend class that wants to call a friendly method in your class must pass itself ($this) to the method it calls as the last parameter
 *
 * See test files for examples
 *
 * Trait Friendly
 * @package MyHeritage\Friends
 */
trait Friendly
{
    private $friendlyMethods;
    private $myNamespace;

    /**
     * Call this method in your constructor as a best practice. Otherwise - this method will be
     * called automatically on the first method call to the class instance.
     *
     * @throws Exception
     */
    private function makeFriends()
    {
        try {
            $reflection = new ReflectionClass($this);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PROTECTED);
            foreach ($methods as $method) {
                $comment = $method->getDocComment();
                if (!is_string($comment) || !preg_match('/@friendly/', $comment)) {
                    continue;
                }
                $this->friendlyMethods[] = $method->getName();
            }
            $this->myNamespace = $reflection->getNamespaceName();
        } catch (Exception $e) {
            throw new FriendlyInitializationException("Failed to initialize freindly methods", 0, $e);
        }
        if (empty($this->friendlyMethods)) {
            throw new FriendlyInitializationException("No friendly method specified");
        }
    }

    /**
     * This magic function is called when someone is trying to access a non-public method of a class
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (!isset($this->friendlyMethods) || !isset($this->myNamespace)) {
            $this->makeFriends();
        }
        if (strpos($this->getCallerNamespace(), $this->myNamespace) !== 0) {
            throw new Exception("Caller is not of the same namespace, can't be a friend");
        }
        if (!in_array($name, $this->friendlyMethods)) {
            throw new Exception("{$name} is not a friendly method");
        }
        return call_user_func_array([$this, $name], $arguments);
    }

    /**
     * @return string The caller object namespace
     * @throws Exception
     */
    private function getCallerNamespace(): string {
        // <caller-method> -> __call -> getCallerNamespace() (need to go back 3 steps)
        $backtrace = debug_backtrace(0, 3);
        if (count($backtrace) < 3 || empty($backtrace[2]['class'])) {
            throw new Exception("Cannot resolve caller namespace");
        }
        return implode('\\', explode('\\', $backtrace[2]['class'], -1));
    }
}