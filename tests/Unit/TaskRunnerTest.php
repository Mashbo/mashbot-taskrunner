<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\TaskRunner;

class TaskRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;

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
        $wasCalled = false;
        $this->sut->add('a', function() use (&$wasCalled) {
            $wasCalled = true;
        });
        $this->sut->invoke('a');
        $this->assertTrue($wasCalled);
    }
}