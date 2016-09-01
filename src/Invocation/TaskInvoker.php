<?php

namespace Mashbo\Mashbot\TaskRunner\Invocation;

use Mashbo\Mashbot\TaskRunner\Exceptions\CannotAutomaticallyInjectParameterException;
use Mashbo\Mashbot\TaskRunner\Inspection\TaskDefinition;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Psr\Log\LoggerInterface;

interface TaskInvoker
{
    public function invoke($task, TaskContext $context);
}