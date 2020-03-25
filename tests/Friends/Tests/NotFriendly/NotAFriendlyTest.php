<?php
namespace MyHeritage\Friends\Tests\NotFriendly;

use Exception;
use MyHeritage\Friends\Tests\Friendly\AFriendlyClass;
use PHPUnit\Framework\TestCase;

class NotAFriendlyTest extends TestCase
{
    public function testFriendlyMethod()
    {
        $this->expectExceptionObject(new Exception("Caller is not of the same namespace, can't be a friend"));

        $friendlyClass = new AFriendlyClass();
        $message = "test";
        $result = $friendlyClass->friendlyMethod($message, $this);
        $this->assertEquals($message, $result);
    }
}