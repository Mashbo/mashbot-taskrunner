<?php

namespace Mashbo\Mashbot\TaskRunner\Hooks\AfterTask;

use Mashbo\Mashbot\TaskRunner\TaskRunner;

class AfterTaskContext
{
    private $arguments;
    /**
     * @var TaskRunner
     */
    private $taskRunner;

    public function __construct(TaskRunner $taskRunner, $arguments)
    {
        $this->arguments = $arguments;
        $this->taskRunner = $taskRunner;
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
