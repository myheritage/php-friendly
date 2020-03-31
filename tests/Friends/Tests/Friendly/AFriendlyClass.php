<?php
namespace MyHeritage\Friends\Tests\Friendly;

use MyHeritage\Friends\Friendly;

class AFriendlyClass
{
    use Friendly;

    /**
     * @friendly
     * @param $message
     *
     * @return mixed
     */
    protected function friendlyMethod($message)
    {
        return $message;
    }

    protected function notFriendly()
    {
        return "Booo";
    }
}