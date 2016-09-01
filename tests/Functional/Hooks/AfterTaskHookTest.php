<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Configuration\TaskRunnerFactory;
use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\AfterTask\AfterTaskContext;
use Mashbo\Mashbot\TaskRunner\Invocation\DirectTaskInvoker;
use Mashbo\Mashbot\TaskRunner\Invocation\DispatchingTaskInvoker;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\Tests\Support\MockableTaskRunnerExtension;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class AfterTaskHookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = TaskRunnerFactory::create();
    }

    public function test_multiple_after_hooks_are_all_run()
    {
        $task1WasRun = false;
        $this->sut->add('task1', function() use (&$task1WasRun) {
            $task1WasRun = true;
        });
        $task2WasRun = false;
        $this->sut->add('task2', function() use (&$task2WasRun) {
            $task2WasRun = true;
        });
        $this->sut->after('task1', function(AfterTaskContext $context) {
            $context->taskRunner()->invoke('task2');
        });
        $this->sut->invoke('task1');

        $this->assertTrue($task1WasRun);
        $this->assertTrue($task2WasRun);
    }

    public function test_defining_task_after_defining_hook_will_not_overwrite_hook()
    {
        $hookWasRun = false;
        $taskWasRun = false;
        $this->sut->after('task', function() use (&$hookWasRun) {
            $hookWasRun = true;
        });
        $this->sut->add('task', function() use (&$taskWasRun) {
            $taskWasRun = true;
        });

        $this->sut->invoke('task');

        $this->assertTrue($hookWasRun);
        $this->assertTrue($taskWasRun);
    }
}
