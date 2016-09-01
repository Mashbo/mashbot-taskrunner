<?php

namespace Mashbo\Mashbot\TaskRunner\Invocation;

use Mashbo\Mashbot\TaskRunner\Configuration\MutableTaskList;
use Mashbo\Mashbot\TaskRunner\Hooks\AfterTask\AfterTaskContext;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
use Mashbo\Mashbot\TaskRunner\TaskContext;

class DispatchingTaskInvoker implements TaskInvoker
{
    /**
     * @var callable[][][]
     */
    private $hooks = [
        'beforeFirstTask' => [],
        'before' => [],
        'after' => [],
    ];

    /**
     * @var MutableTaskList
     */
    private $tasks;
    private $beforeFirstTaskHooksRun = false;

    public function __construct(MutableTaskList $tasks)
    {
        $this->tasks = $tasks;
    }

    public function beforeFirstTask(callable $callable)
    {
        $this->hooks['beforeFirstTask'][] = $callable;
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
        if (!array_key_exists($taskName, $this->hooks['before'])) {
            return;
        }
        foreach ($this->hooks['before'][$taskName] as $hook) {
            call_user_func($hook, $context);
        }
    }

    private function dispatchAfterHook($taskName, AfterTaskContext $context)
    {
        if (!array_key_exists($taskName, $this->hooks['after'])) {
            return;
        }

        foreach ($this->hooks['after'][$taskName] as $hook) {
            call_user_func($hook, $context);
        }
    }

    public function invoke($task, TaskContext $context)
    {
        if (!$this->beforeFirstTaskHooksRun) {
            $this->dispatchBeforeFirstTask($context);
        }

        $args = $context->arguments();

        $beforeTaskContext = new BeforeTaskContext($args);
        $this->dispatchBeforeHook($task, $beforeTaskContext);

        $context = $context->withArguments($beforeTaskContext->arguments());

        $callable = $this->tasks->find($task);

        $taskResult = call_user_func_array(
            $callable,
            ArgumentResolver::resolveParametersToCallable($callable, $context)
        );

        $afterTaskContext = new AfterTaskContext($context->taskRunner(), $args);
        $this->dispatchAfterHook($task, $afterTaskContext);

        return $taskResult;
    }

    private function dispatchBeforeFirstTask(TaskContext $context)
    {
        foreach ($this->hooks['beforeFirstTask'] as $hook) {
            $hook($context);
        }
        $this->beforeFirstTaskHooksRun = true;
    }
}