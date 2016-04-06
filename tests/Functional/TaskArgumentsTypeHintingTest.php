<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Exceptions\CannotAutomaticallyInjectParameterException;
use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\Tests\Support\MockableTaskRunnerExtension;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TaskArgumentsTypeHintingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;
    private $invokeWasCalled        = false;

    protected function setUp()
    {
        $this->sut = new TaskRunner(new NullLogger());
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

    public function test_named_parameters_are_injected()
    {
        $this->sut->add('task', function($arg1, $anotherArgument) {
            $this->assertEquals('arg1',         $arg1);
            $this->assertEquals('anotherValue', $anotherArgument);
        });
        $this->sut->invoke('task', ['arg1' => 'arg1', 'anotherArgument' => 'anotherValue']);
    }

    public function test_exception_is_thrown_if_typehinted_arg_is_unknown()
    {
        $this->sut->add('task', function(\DateTime $time, $argument) {});
        $this->expectException(CannotAutomaticallyInjectParameterException::class);
        $this->sut->invoke('task', ['argument' => 'value']);
    }

    public function test_named_parameters_are_injected_for_method_callable()
    {
        $this->sut->add('task', [$this, 'exampleCallableWithNamedParameters']);
        $this->sut->invoke('task', ['parameter1' => 'value1', 'parameter2' => 'value2']);
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


    public function exampleCallableWithContext(TaskContext $context)
    {

    }

    public static function exampleStaticCallableWithContext(TaskContext $context)
    {

    }

    public function exampleCallableWithNamedParameters($parameter1, $parameter2)
    {
        $this->assertEquals('value1', $parameter1);
        $this->assertEquals('value2', $parameter2);
    }

    public function __invoke(TaskContext $context)
    {
        $this->invokeWasCalled = true;
    }
}
