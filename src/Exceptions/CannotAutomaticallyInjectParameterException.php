<?php

namespace Mashbo\Mashbot\TaskRunner\Exceptions;

class CannotAutomaticallyInjectParameterException extends \RuntimeException
{
    public function __construct($paramName)
    {
        parent::__construct("Cannot automatically inject value for parameter named $paramName");
    }
}
