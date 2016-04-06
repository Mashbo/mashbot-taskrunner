<?php

namespace Mashbo\Mashbot\TaskRunner;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
use Mashbo\Mashbot\TaskRunner\Invocation\TaskInvoker;
use Psr\Log\LoggerInterface;

class TaskRunner
{
    private $tasks = [];

    /**
     * @var callable[][][]
     */
    private $hooks = [
        'before' => []
    ];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TaskInvoker
     */
    private $taskInvoker;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->taskInvoker = new TaskInvoker();
    }

    /**
     * @param $task
     * @param callable $callable
     */
    public function add($task, callable $callable)
    {
        $this->tasks[$task] = $callable;
        $this->hooks['before'][$task] = [];
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

    public function before($task, callable $beforeHook)
    {
        $this->hooks['before'][$task][] = $beforeHook;
    }

    private function dispatchBeforeHook($taskName, BeforeTaskContext $context)
    {
        foreach ($this->hooks['before'][$taskName] as $hook) {
            call_user_func($hook, $context);
        }
    }

    public function invoke($task, array $args = [])
    {
        $taskCallable = $this->locateCallable($task);

        $beforeTaskContext = new BeforeTaskContext($args);
        $this->dispatchBeforeHook($task, $beforeTaskContext);

        return $this->taskInvoker->invokeCallable($taskCallable, new TaskContext($this, $this->logger, $beforeTaskContext->arguments()));
    }

    public function extend(TaskRunnerExtension $extension)
    {
        $extension->amendTasks($this);
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
