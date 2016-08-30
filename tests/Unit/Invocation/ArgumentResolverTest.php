<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit\Invocation;

use Mashbo\Mashbot\TaskRunner\Configuration\MutableTaskList;
use Mashbo\Mashbot\TaskRunner\Invocation\ArgumentResolver;
use Mashbo\Mashbot\TaskRunner\Invocation\DirectTaskInvoker;
use Mashbo\Mashbot\TaskRunner\Invocation\DispatchingTaskInvoker;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Psr\Log\NullLogger;

class ArgumentResolverTest extends \PHPUnit_Framework_TestCase
{
    public function test_callable_can_typehint_context()
    {
        $callable = function(TaskContext $contextToInject) {};

        $tasks = new MutableTaskList();
        $logger = new NullLogger();
        $invoker = new DispatchingTaskInvoker($tasks);
        $taskContext = new TaskContext(
            new TaskRunner($logger, $invoker, $tasks),
            $logger,
            []
        );

        $args = ArgumentResolver::resolveParametersToCallable(
            $callable,
            $taskContext
        );

        $this->assertCount(1, $args);
        $this->assertSame($taskContext, $args[0]);
    }

    public function test_resolved_args_can_take_defaults()
    {
        $callable = function($a = 1, $b = 'string') {};

        $tasks = new MutableTaskList();
        $logger = new NullLogger();
        $invoker = new DispatchingTaskInvoker($tasks);
        $taskContext = new TaskContext(
            new TaskRunner($logger, $invoker, $tasks),
            $logger,
            []
        );

        $args = ArgumentResolver::resolveParametersToCallable(
            $callable,
            $taskContext
        );

        $this->assertCount(2, $args);

        $this->assertSame(1,        $args[0]);
        $this->assertSame('string', $args[1]);

    }
}