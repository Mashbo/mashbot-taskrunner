<?php

namespace spec\Mashbo\Mashbot\TaskRunner;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TaskRunnerSpec extends ObjectBehavior
{
    function it_throws_exception_if_task_is_invoked_but_not_defined()
    {
        $this->shouldThrow()->duringInvoke('undefined:task');
    }
}
