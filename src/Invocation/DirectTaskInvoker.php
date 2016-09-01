<?php

namespace Mashbo\Mashbot\TaskRunner\Invocation;

use Mashbo\Mashbot\TaskRunner\Configuration\MutableTaskList;
use Mashbo\Mashbot\TaskRunner\TaskContext;

class DirectTaskInvoker implements TaskInvoker
{
    /**
     * @var MutableTaskList
     */
    private $tasks;

    public function __construct(MutableTaskList $tasks)
    {
        $this->tasks = $tasks;
    }

    public function invoke($task, TaskContext $context)
    {

    }

}