<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Support;

use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\TaskRunnerExtension;

class MockableTaskRunnerExtension implements TaskRunnerExtension
{
    private $tasksToAdd = [];

    public function willAddTask($taskName, callable $callable)
    {
        $this->tasksToAdd[$taskName] = $callable;
    }

    public function amendTasks(TaskRunner $taskRunner)
    {
        foreach ($this->tasksToAdd as $task => $callback) {
            $taskRunner->add($task, $callback);
        }
    }
}