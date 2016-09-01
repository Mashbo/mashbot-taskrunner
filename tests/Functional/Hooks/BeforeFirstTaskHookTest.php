<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Configuration\TaskRunnerFactory;
use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
use Mashbo\Mashbot\TaskRunner\Invocation\DirectTaskInvoker;
use Mashbo\Mashbot\TaskRunner\Invocation\DispatchingTaskInvoker;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\Tests\Support\MockableTaskRunnerExtension;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class BeforeFirstTaskHookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = TaskRunnerFactory::create();
    }

    public function test_before_first_task_called_only_once()
    {
        $this->sut->add('task1', function(TaskContext $context) {});
        $this->sut->add('task2', function(TaskContext $context) {});

        $this->sut->beforeFirstTask(function(TaskContext $context) use (&$wasCalled) {
            $wasCalled = true;
        });

        $wasCalled = false;
        $this->sut->invoke('task1');
        $this->assertTrue($wasCalled);

        $wasCalled = false;
        $this->sut->invoke('task1');
        $this->assertFalse($wasCalled);
    }
}
