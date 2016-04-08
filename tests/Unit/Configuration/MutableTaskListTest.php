<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit\Configuration;

use Mashbo\Mashbot\TaskRunner\Configuration\Exceptions\UndefinedTaskException;
use Mashbo\Mashbot\TaskRunner\Configuration\MutableTaskList;

class MutableTaskListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MutableTaskList
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new MutableTaskList();
    }

    public function test_count_is_initially_zero()
    {
        $this->assertCount(0, $this->sut);
    }

    public function test_it_throws_exception_when_trying_to_find_unknown_task()
    {
        $this->expectException(UndefinedTaskException::class);
        $this->sut->find('task');
    }

    public function test_it_can_add_and_find_tasks()
    {
        $this->sut->add('task', function () {});
        $this->assertInstanceOf(\Closure::class, $this->sut->find('task'));
    }
}