<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit\Hooks\BeforeTask;

use Mashbo\Mashbot\TaskRunner\Hooks\AfterTask\AfterTaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;

class AfterTaskContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AfterTaskContext
     */
    private $sut;

    public function test_it_returns_all_arguments()
    {
        $runner = $this->prophesize(TaskRunner::class);

        $this->sut = new AfterTaskContext($runner->reveal(), ['a' => 'b', 'c' => 'd']);
        $this->assertEquals(['a' => 'b', 'c' => 'd'], $this->sut->arguments());
        $this->assertEquals($runner->reveal(), $this->sut->taskRunner());
    }
}
