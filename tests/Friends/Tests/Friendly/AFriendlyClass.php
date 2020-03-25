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
    protected function friendlyMethod($message, /** @noinspection PhpUnusedParameterInspection  */ $caller = null)
    {
        return $message;
    }

    protected function notFriendly()
    {
        return "Booo";
    }
}