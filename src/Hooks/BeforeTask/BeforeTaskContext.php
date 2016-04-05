<?php

namespace Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask;

class BeforeTaskContext
{
    private $arguments;

    public function __construct($initialArguments)
    {
        $this->arguments = $initialArguments;
    }

    public function setArgument($key, $value)
    {
        $this->arguments[$key] = $value;
    }

    public function argument($value)
    {
        return $this->arguments[$value];
    }

    public function arguments()
    {
        return $this->arguments;
    }
}
