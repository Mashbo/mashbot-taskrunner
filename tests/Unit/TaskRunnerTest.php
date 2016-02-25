<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\TaskContext;
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

    public function test_task_context_will_be_injected_if_anon_function_type_hints_it()
    {
        $this->sut->add('task:with:context', function(TaskContext $context) {

        });
        $this->sut->invoke('task:with:context');
    }

    public function test_task_context_will_be_injected_if_object_array_callback_type_hints_it()
    {
        $this->sut->add('task:with:context', [$this, 'exampleCallableWithContext']);
        $this->sut->invoke('task:with:context');
    }

    public function test_task_context_will_be_injected_if_static_class_array_callback_type_hints_it()
    {
        $this->sut->add('task:with:context', [TaskRunnerTest::class, 'exampleStaticCallableWithContext']);
        $this->sut->invoke('task:with:context');
    }

    public function test_task_context_will_be_injected_if_static_class_string_callback_type_hints_it()
    {
        $this->sut->add('task:with:context', TaskRunnerTest::class.'::exampleStaticCallableWithContext');
        $this->sut->invoke('task:with:context');
    }

    /**
     * @depends test_task_context_will_be_injected_if_anon_function_type_hints_it
     */
    public function test_task_context_is_initialised_with_arguments_from_invoke()
    {
        $this->sut->add('task:with:args', function(TaskContext $context) {
            $this->assertEquals('bar', $context->argument('foo'));
        });
        $this->sut->invoke('task:with:args', ['foo' => 'bar']);
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
}