<?php

namespace Mashbo\Mashbot\TaskRunner;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
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

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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

        return $this->invokeCallable($taskCallable, $beforeTaskContext->arguments());
    }

    public function extend(TaskRunnerExtension $extension)
    {
        $extension->amendTasks($this);
    }

    private function invokeCallable(callable $task, array $args)
    {
        switch (true) {
            case (is_object($task) && ($task instanceof \Closure)):
                $parameters = (new \ReflectionFunction($task))->getParameters();
                break;
            case (is_object($task) && method_exists($task, '__invoke')):
                $parameters = (new \ReflectionClass($task))->getMethod('__invoke')->getParameters();
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
            return call_user_func_array($task, [new TaskContext($this, $this->logger, $args)]);
        }

        return call_user_func_array($task, []);
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
