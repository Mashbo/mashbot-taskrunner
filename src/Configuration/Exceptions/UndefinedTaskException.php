<?php

namespace Mashbo\Mashbot\TaskRunner\Configuration\Exceptions;

class UndefinedTaskException extends \RuntimeException
{
    public function __construct($task)
    {
        parent::__construct("Task $task has not been added and cannot be inspected");
    }
}
