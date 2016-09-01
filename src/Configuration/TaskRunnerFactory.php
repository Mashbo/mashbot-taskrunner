<?php

namespace Mashbo\Mashbot\TaskRunner\Configuration;

use Mashbo\Mashbot\TaskRunner\Invocation\DirectTaskInvoker;
use Mashbo\Mashbot\TaskRunner\Invocation\DispatchingTaskInvoker;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TaskRunnerFactory
{
    public static function create(LoggerInterface $logger = null)
    {
        $logger = is_null($logger)
            ? new NullLogger()
            : $logger;

        $tasks = new MutableTaskList();

        return new TaskRunner(
            $logger,
            new DispatchingTaskInvoker($tasks),
            $tasks
        );
    }
}