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

class TaskRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;
    private $testCallableWasCalled  = false;
    private $invokeWasCalled        = false;

    protected function setUp()
    {
        $this->sut = TaskRunnerFactory::create();
    }

    public function test_it_throws_exception_if_task_is_invoked_but_not_defined()
    {
        $this->expectException(TaskNotDefinedException::class);
        $this->sut->invoke('a');
    }

    public function test_it_invokes_task_if_it_has_been_defined()
    {
        $this->sut->add('a', [$this, 'exampleCallable']);
        $this->sut->invoke('a');
        $this->assertTrue($this->testCallableWasCalled);
    }

    /**
     * @depends test_it_throws_exception_if_task_is_invoked_but_not_defined
     */
    public function test_tasks_can_be_defined_in_registered_extensions()
    {
        $extension = new MockableTaskRunnerExtension();
        $extension->willAddTask('a', [$this, 'exampleCallable']);

        $this->sut->extend($extension);
        $this->sut->invoke('a');
        $this->assertTrue($this->testCallableWasCalled);
    }

    public function test_tasks_can_return_values()
    {
        $this->sut->add('task', function() {
            return 'value';
        });

        $this->assertEquals('value', $this->sut->invoke('task'));
    }


    public function exampleCallable()
    {
        $this->testCallableWasCalled = true;
    }

    public function exampleCallableWithContext(TaskContext $context)
    {

    }

    public static function exampleStaticCallableWithContext(TaskContext $context)
    {

    }

    public function __invoke(TaskContext $context)
    {
        $this->invokeWasCalled = true;
    }
}