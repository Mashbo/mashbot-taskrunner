<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
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
        $this->sut = new TaskRunner(new NullLogger());
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

    public function test_task_context_will_be_injected_if_callable_object_type_hints_it()
    {
        $this->sut->add('task:with:context', $this);
        $this->sut->invoke('task:with:context');
    }

    public function test_task_context_will_be_injected_if_static_class_string_callback_type_hints_it()
    {
        $this->sut->add('task:with:context', TaskRunnerTest::class.'::exampleStaticCallableWithContext');
        $this->sut->invoke('task:with:context');
    }

    public function test_task_can_be_composed_of_other_tasks()
    {
        $this->sut->addComposed('task', ['task1', 'task2', 'task3']);

        $lastCalled = null;

        $task1wasCalled = false;
        $this->sut->add('task1', function() use (&$task1wasCalled, &$lastCalled) {
            $task1wasCalled = true;
            $this->assertNull($lastCalled);
            $lastCalled = 'task1';
        });

        $task2wasCalled = false;
        $this->sut->add('task2', function() use (&$task2wasCalled, &$lastCalled) {
            $task2wasCalled = true;
            $this->assertEquals('task1', $lastCalled);
            $lastCalled = 'task2';
        });

        $task3wasCalled = false;
        $this->sut->add('task3', function() use (&$task3wasCalled, &$lastCalled) {
            $task3wasCalled = true;
            $this->assertEquals('task2', $lastCalled);
        });

        $this->sut->invoke('task');
        $this->assertTrue($task1wasCalled);
        $this->assertTrue($task2wasCalled);
        $this->assertTrue($task3wasCalled);
    }

    public function test_composed_tasks_inherit_arguments_from_parent()
    {
        $this->sut->addComposed('foo', ['bar']);
        $this->sut->add('bar', function(TaskContext $context) {
            $this->assertEquals('qux', $context->argument('baz'));
        });
        $this->sut->invoke('foo', ['baz' => 'qux']);
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

    public function test_arguments_can_be_added_to_context_with_before_hook()
    {
        $this->sut->add('task', function(TaskContext $context) {
            $this->assertEquals('bar', $context->argument('foo'));
        });
        $this->sut->before('task', function(BeforeTaskContext $context) {
            $context->setArgument('foo', 'bar');
        });
        $this->sut->invoke('task');
    }

    public function test_multiple_before_hooks_are_all_run()
    {
        $this->sut->add('task1', function(TaskContext $context) {
            $this->assertEquals('bar', $context->argument('foo'));
            $this->assertEquals('qux', $context->argument('baz'));
        });
        $this->sut->before('task1', function(BeforeTaskContext $context) {
            $context->setArgument('foo', 'bar');
        });
        $this->sut->before('task1', function(BeforeTaskContext $context) {
            $context->setArgument('baz', 'qux');
        });
        $this->sut->invoke('task1');

        $this->sut->add('task2', function(TaskContext $context) {
            $this->assertEquals('bar2', $context->argument('foo'));
            $this->assertEquals('qux2', $context->argument('baz'));
        });
        $this->sut->before('task2', function(BeforeTaskContext $context) {
            $context->setArgument('foo', 'bar2');
        });
        $this->sut->before('task2', function(BeforeTaskContext $context) {
            $context->setArgument('baz', 'qux2');
        });
        $this->sut->invoke('task2');
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