<?php
namespace MyHeritage\Friends\Tests\Friendly;
use PHPUnit\Framework\TestCase;

class FriendlyTest extends TestCase
{
    public function testFriendlyMethod()
    {
        $friendlyClass = new AFriendlyClass();
        $message = "test";
        $result = $friendlyClass->friendlyMethod($message, $this);
        $this->assertEquals($message, $result);
    }

    public function testNotFriendlyMethod()
    {
        $this->expectExceptionObject(new \Exception('notFriendly is not a friendly method'));
        $friendlyClass = new AFriendlyClass();
        $message = "test";
        $friendlyClass->notFriendly($message, $this);
    }
}