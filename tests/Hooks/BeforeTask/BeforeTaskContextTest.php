<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Hooks\BeforeTask;

use Mashbo\Mashbot\TaskRunner\Hooks\BeforeTask\BeforeTaskContext;

class BeforeTaskContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BeforeTaskContext
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new BeforeTaskContext([]);
    }

    public function testItHasMutableArguments()
    {
        $this->sut->setArgument('a', 'b');
        $this->assertEquals('b', $this->sut->argument('a'));
    }

    public function test_it_returns_all_arguments()
    {
        $this->sut->setArgument('a', 'b');
        $this->sut->setArgument('c', 'd');
        $this->assertEquals(['a' => 'b', 'c' => 'd'], $this->sut->arguments());

    }
}
