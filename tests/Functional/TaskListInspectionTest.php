<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Functional;

use Mashbo\Mashbot\TaskRunner\Inspection\TaskDefinition;
use Mashbo\Mashbot\TaskRunner\Inspection\TaskParameterList;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Psr\Log\NullLogger;

class TaskListInspectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new TaskRunner(new NullLogger());
    }

    public function test_task_list_is_initially_empty()
    {
        $taskList = $this->sut->tasks();
        $this->assertInstanceOf(\Countable::class, $taskList);
        $this->assertCount(0, $taskList);
    }

    public function test_inspectable_tasks_expose_parameters()
    {
        $this->sut->add('task', function($a, $b) {

        });
        $taskDefinition = $this->sut->tasks()->find('task');
        $this->assertInstanceOf(TaskDefinition::class, $taskDefinition);

        $taskParams = $taskDefinition->parameters();
        $this->assertInstanceOf(TaskParameterList::class, $taskParams);

        $expectedParamNames = ['a', 'b'];
        $actualParamNames = [];
        foreach ($taskParams as $param) {
            $actualParamNames[] = $param->name();
        }

        $this->assertEquals($expectedParamNames, $actualParamNames);
    }
}
