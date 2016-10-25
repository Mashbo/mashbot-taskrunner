<?php

namespace Mashbo\Mashbot\TaskRunner\Hooks\AfterTask;

use Mashbo\Mashbot\TaskRunner\TaskRunner;

class AfterTaskContext
{
    private $taskName;
    private $arguments;
    /**
     * @var TaskRunner
     */
    private $taskRunner;

    public function __construct($taskName, TaskRunner $taskRunner, $arguments)
    {
        $this->taskName = $taskName;
        $this->arguments = $arguments;
        $this->taskRunner = $taskRunner;
    }

    public function taskName()
    {
        return $this->taskName;
    }

    /**
     * @return TaskRunner
     */
    public function taskRunner()
    {
        return $this->taskRunner;
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
