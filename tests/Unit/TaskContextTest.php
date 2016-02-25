<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;

class TaskContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $taskRunner;

    /**
     * @var TaskContext
     */
    private $sut;

    protected function setUp()
    {
        $this->taskRunner = new TaskRunner();
        $this->sut = new TaskContext(
            $this->taskRunner,
            [
                'array' => ['a' => 'b']
            ]
        );
    }

    public function test_it_holds_runner()
    {
        $this->assertSame($this->taskRunner, $this->sut->taskRunner());
    }

    public function test_it_holds_config()
    {
        $this->assertSame(['array' => ['a' => 'b']],    $this->sut->arguments());
        $this->assertSame(['a' => 'b'],                 $this->sut->argument('array'));
    }
}