<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit;

use Mashbo\Mashbot\TaskRunner\Configuration\TaskRunnerFactory;
use Mashbo\Mashbot\TaskRunner\Exceptions\TaskNotDefinedException;
use Mashbo\Mashbot\TaskRunner\Hooks\AfterTask\AfterTaskContext;
use Mashbo\Mashbot\TaskRunner\Invocation\DirectTaskInvoker;
use Mashbo\Mashbot\TaskRunner\Invocation\DispatchingTaskInvoker;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\Tests\Support\MockableTaskRunnerExtension;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class AfterAnyTaskHookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskRunner
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = TaskRunnerFactory::create();
    }

    public function test_hook_is_run()
    {
        $hookWasRun = false;
        $this->sut->afterAnyTask(function() use (&$hookWasRun) {
            $hookWasRun = true;
        });
        $this->sut->add('task', function() {
        });
        $this->sut->invoke('task');

        $this->assertTrue($hookWasRun);
    }
}
