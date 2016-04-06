<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\Tests\Support\MockableTaskRunnerExtension;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TaskCompositionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new TaskRunner(new NullLogger());
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
}
