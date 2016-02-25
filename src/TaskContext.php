<?php

namespace Mashbo\Mashbot\TaskRunner;

class TaskContext
{
    /**
     * @var TaskRunner
     */
    private $taskRunner;
    private $arguments;

    public function __construct(TaskRunner $taskRunner, $arguments)
    {
        $this->taskRunner = $taskRunner;
        $this->arguments = $arguments;
    }

    public function taskRunner()
    {
        return $this->taskRunner;
    }

    public function arguments()
    {
        return $this->arguments;
    }

    public function argument($name)
    {
        return $this->arguments[$name];
    }
}