<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit\Configuration;

use Mashbo\Mashbot\TaskRunner\Configuration\TaskRunnerFactory;
use Mashbo\Mashbot\TaskRunner\TaskRunner;

class TaskRunnerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_object_is_instantiated()
    {
        $this->assertInstanceOf(TaskRunner::class, TaskRunnerFactory::create());
    }
}