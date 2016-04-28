<?php

namespace Mashbo\Mashbot\TaskRunner;

use Mashbo\Mashbot\TaskRunner\Configuration\Exceptions\UndefinedTaskException;
use Mashbo\Mashbot\TaskRunner\Configuration\MutableTaskList;
use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\AfterTask\AfterTaskContext;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
use Mashbo\Mashbot\TaskRunner\Inspection\TaskList;
use Mashbo\Mashbot\TaskRunner\Invocation\TaskInvoker;
use Psr\Log\LoggerInterface;

class TaskRunner
{
    private $tasks;

    /**
     * @var callable[][][]
     */
    private $hooks = [
        'before' => [],
        'after' => [],
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
        $this->logger      = $logger;
        $this->taskInvoker = new TaskInvoker();
        $this->tasks       = new MutableTaskList([]);
    }

    public function tasks()
    {
        return new TaskList($this->tasks);
    }

    /**
     * @param $task
     * @param callable $callable
     */
    public function add($task, callable $callable)
    {
        $this->tasks->add($task, $callable);
        if (!array_key_exists($task, $this->hooks['before'])) {
            $this->hooks['before'][$task] = [];
        }
        if (!array_key_exists($task, $this->hooks['after'])) {
            $this->hooks['after'][$task] = [];
        }
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

    public function after($task, callable $afterHook)
    {
        $this->hooks['after'][$task][] = $afterHook;
    }

    private function dispatchBeforeHook($taskName, BeforeTaskContext $context)
    {
        foreach ($this->hooks['before'][$taskName] as $hook) {
            call_user_func($hook, $context);
        }
    }

    private function dispatchAfterHook($taskName, AfterTaskContext $context)
    {
        foreach ($this->hooks['after'][$taskName] as $hook) {
            call_user_func($hook, $context);
        }
    }

    public function invoke($task, array $args = [])
    {
        $taskCallable = $this->locateCallable($task);

        $beforeTaskContext = new BeforeTaskContext($args);
        $this->dispatchBeforeHook($task, $beforeTaskContext);

        $taskResult = $this->taskInvoker->invokeCallable(
            $taskCallable,
            new TaskContext($this, $this->logger, $beforeTaskContext->arguments())
        );

        $afterTaskContext = new AfterTaskContext($this, $args);
        $this->dispatchAfterHook($task, $afterTaskContext);

        return $taskResult;
    }

    public function extend(TaskRunnerExtension $extension)
    {
        $extension->amendTasks($this);
    }

    /**
     * @param $task
     * @return callable
     */
    private function locateCallable($task)
    {
        try {
            return $this->tasks->find($task);
        } catch (UndefinedTaskException $e) {
            throw new TaskNotDefinedException($task);
        }
    }
}
