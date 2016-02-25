<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\Tests\Support\MockableTaskRunnerExtension;

class TaskRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;
    private $testCallableWasCalled = false;

    protected function setUp()
    {
        $this->sut = new TaskRunner();
    }

    public function test_it_throws_exception_if_task_is_invoked_but_not_defined()
    {
        $this->expectException(TaskNotDefinedException::class);
        $this->sut->invoke('a');
    }

    public function test_it_invokes_task_if_it_has_been_defined()
    {
        $this->sut->add('a', [$this, 'testCallable']);
        $this->sut->invoke('a');
        $this->assertTrue($this->testCallableWasCalled);
    }

    /**
     * @depends test_it_throws_exception_if_task_is_invoked_but_not_defined
     */
    public function test_tasks_can_be_defined_in_registered_extensions()
    {
        $extension = new MockableTaskRunnerExtension();
        $extension->willAddTask('a', [$this, 'testCallable']);

        $this->sut->extend($extension);
        $this->sut->invoke('a');
        $this->assertTrue($this->testCallableWasCalled);
    }

    public function testCallable()
    {
        $this->testCallableWasCalled = true;
    }
}