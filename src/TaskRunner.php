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
        $this->invokeCallable($this->locateCallable($task));
    }

    public function extend(TaskRunnerExtension $extension)
    {
        $extension->amendTasks($this);
    }

    /**
     * @param $task
     */
    private function invokeCallable(callable $task)
    {
        switch (true) {
            case (is_object($task) && ($task instanceof \Closure)):
                $parameters = (new \ReflectionFunction($task))->getParameters();
                break;
            case (is_array($task) && 2 == count($task)):
                $parameters = (new \ReflectionClass($task[0]))->getMethod($task[1])->getParameters();
                break;
            case (is_string($task) && false !== strpos($task, '::')):
                $parts = explode('::', $task);
                $parameters = (new \ReflectionClass($parts[0]))->getMethod($parts[1])->getParameters();
                break;
            default:
                throw new \LogicException("Cannot reflect callable type. This type of callable is not yet supported.");
        }

        if (
            1 == count($parameters) &&
            $parameters[0]->getClass() &&
            $parameters[0]->getClass()->getName() == TaskContext::class
        ) {
            call_user_func_array($task, [new TaskContext($this, [])]);
            return;
        }

        call_user_func_array($task, []);
    }

    /**
     * @param $task
     * @return mixed
     */
    private function locateCallable($task)
    {
        if (!array_key_exists($task, $this->tasks)) {
            throw new TaskNotDefinedException($task);
        }

        return $this->tasks[$task];
    }
}
