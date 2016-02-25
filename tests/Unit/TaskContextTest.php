<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Exceptions\ArgumentNotSetException;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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

    /**
     * @var LoggerInterface
     */
    private $logger;

    protected function setUp()
    {
        $this->logger = new NullLogger();
        $this->taskRunner = new TaskRunner($this->logger);
        $this->sut = new TaskContext(
            $this->taskRunner,
            $this->logger,
            [
                'array' => ['a' => 'b']
            ]
        );
    }

    public function test_it_exposes_logger_to_tasks()
    {
        $this->assertSame($this->logger, $this->sut->logger());
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

    public function test_it_throws_exception_when_asked_for_arg_not_set()
    {
        $this->expectException(ArgumentNotSetException::class);
        $this->sut->argument('undefined');
    }
}
