<?php

namespace Mashbo\Mashbot\TaskRunner;

use Mashbo\Mashbot\TaskRunner\Configuration\MutableTaskList;
use Mashbo\Mashbot\TaskRunner\Inspection\TaskList;
use Mashbo\Mashbot\TaskRunner\Invocation\DispatchingTaskInvoker;
use Psr\Log\LoggerInterface;

class TaskRunner
{
    private $tasks;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DispatchingTaskInvoker
     */
    private $dispatchingTaskInvoker;

    public function __construct(LoggerInterface $logger, DispatchingTaskInvoker $taskInvoker, MutableTaskList $tasks)
    {
        $this->logger                   = $logger;
        $this->dispatchingTaskInvoker   = $taskInvoker;
        $this->tasks                    = $tasks;
    }

    public function tasks()
    {
        return new TaskList($this->tasks);
    }

    public function beforeFirstTask(callable $callable)
    {
        $this->dispatchingTaskInvoker->beforeFirstTask($callable);
    }

    public function before($task, callable $beforeHook)
    {
        $this->dispatchingTaskInvoker->before($task, $beforeHook);
    }

    public function after($task, callable $afterHook)
    {
        $this->dispatchingTaskInvoker->after($task, $afterHook);
    }

    /**
     * @param $task
     * @param callable $callable
     */
    public function add($task, callable $callable)
    {
        $this->tasks->add($task, $callable);
    }

    public function addComposed($task, $composedTasks)
    {
        $this->add($task, function(TaskContext $context) use ($composedTasks) {

            $args = $context->arguments();
            foreach ($composedTasks as $task) {
                $this->invoke($task, $args);
            }
        });
    }

    public function invoke($task, array $args = [])
    {
        return $this->dispatchingTaskInvoker->invoke(
            $task,
            new TaskContext($this, $this->logger, $args)
        );
    }

    public function extend(TaskRunnerExtension $extension)
    {
        $extension->amendTasks($this);
    }
}
