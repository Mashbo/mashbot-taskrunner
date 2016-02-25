<?php

namespace Mashbo\Mashbot\TaskRunner;

use Mashbo\Mashbot\TaskRunner\Exceptions\ArgumentNotSetException;

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
        if (!array_key_exists($name, $this->arguments)) {
            throw new ArgumentNotSetException($name, $this->arguments);
        }
        return $this->arguments[$name];
    }
}