<?php

namespace Mashbo\Mashbot\TaskRunner\Exceptions;

class TaskNotDefinedException extends \RuntimeException
{
    public function __construct($task)
    {
        parent::__construct("Task $task has not been defined");
    }
}