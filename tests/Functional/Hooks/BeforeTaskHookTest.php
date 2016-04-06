<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\Tests\Support\MockableTaskRunnerExtension;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class BeforeTaskHookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new TaskRunner(new NullLogger());
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

    public function test_defining_task_after_defining_hook_will_not_overwrite_hook()
    {
        $hookWasRun = false;
        $taskWasRun = false;
        $this->sut->before('task', function() use (&$hookWasRun) {
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
