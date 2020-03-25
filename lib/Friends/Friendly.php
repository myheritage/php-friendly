<?php
namespace MyHeritage\Friends;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Use this trait to enable making protected methods friends with classes of the same namespace
 * In order to make "friendly" methods you will need to:
 * 1. use this trait
 * 2. Annotate protected methods that you want to expose with @friendly annotation
 * 3. Add $caller optional parameter to the for the sake of better phpdoc
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
     * @throws ReflectionException
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (!isset($this->friendlyMethods)) {
            $this->makeFriends();
        }
        if (count($arguments) < 1) {
            throw new Exception("Must have at least one parameter which is the caller object");
        }
        $caller = end($arguments);
        if (!is_object($caller)) {
            throw new Exception("last parameter must be an object");
        }
        $caller = new ReflectionClass($caller);
        $me = new ReflectionClass($this);
        if (strpos($caller->getNamespaceName(), $me->getNamespaceName()) !== 0) {
            throw new Exception("Caller is not of the same namespace, can't be a friend");
        }
        if (!in_array($name, $this->friendlyMethods)) {
            throw new Exception("{$name} is not a friendly method");
        }
        return call_user_func_array([$this, $name], array_slice($arguments,0, -1));
    }
}