<?php

namespace Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask;

class BeforeTaskContext
{
    private $taskName;
    private $arguments;

    public function __construct($taskName, $initialArguments)
    {
        $this->taskName = $taskName;
        $this->arguments = $initialArguments;
    }

    public function taskName()
    {
        return $this->taskName;
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
