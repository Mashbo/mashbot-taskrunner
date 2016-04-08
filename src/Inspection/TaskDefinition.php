<?php

namespace Mashbo\Mashbot\TaskRunner\Inspection;

class TaskDefinition
{
    /**
     * @var callable
     */
    private $task;

    public function __construct(callable $task)
    {
        $this->task = $task;
    }

    /**
     * @return TaskParameterList
     */
    public function parameters()
    {
        return TaskParameterList::fromReflectionParameterArray($this->rawParameters());
    }

    public function rawParameters()
    {
        $task = $this->task;

        switch (true) {
            case (is_object($task) && ($task instanceof \Closure)):
                return (new \ReflectionFunction($task))->getParameters();
            case (is_object($task) && method_exists($task, '__invoke')):
                return (new \ReflectionClass($task))->getMethod('__invoke')->getParameters();
            case (is_array($task) && 2 == count($task)):
                return (new \ReflectionClass($task[0]))->getMethod($task[1])->getParameters();
            case (is_string($task) && false !== strpos($task, '::')):
                $parts = explode('::', $task);
                return (new \ReflectionClass($parts[0]))->getMethod($parts[1])->getParameters();
            default:
                throw new \LogicException("Cannot reflect callable type. This type of callable is not yet supported.");
        }
    }
}
