<?php

namespace Mashbo\Mashbot\TaskRunner\Exceptions;

class ArgumentNotSetException extends \InvalidArgumentException
{
    public function __construct($argument, array $allArguments)
    {
        parent::__construct(
            "Argument $argument not set. Available args are [" . implode(', ', array_keys($allArguments)) . "]"
        );
    }
}