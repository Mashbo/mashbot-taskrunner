<?php

namespace Mashbo\Mashbot\TaskRunner;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;

class TaskRunner
{
    private $tasks = [];

    /**
     * @param $task
     * @param callable $callable
     */
    public function add($task, callable $callable)
    {
        $this->tasks[$task] = $callable;
    }

    public function invoke($task)
    {
        if (!array_key_exists($task, $this->tasks)) {
            throw new TaskNotDefinedException($task);
        }

        $this->tasks[$task]();
    }
}
