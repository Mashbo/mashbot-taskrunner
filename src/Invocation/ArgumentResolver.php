<?php

namespace Mashbo\Mashbot\TaskRunner\Invocation;

use Mashbo\Mashbot\TaskRunner\Exceptions\CannotAutomaticallyInjectParameterException;
use Mashbo\Mashbot\TaskRunner\Inspection\TaskDefinition;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Psr\Log\LoggerInterface;

class ArgumentResolver
{
    public static function resolveParametersToCallable(callable $callable, TaskContext $context)
    {
        $callableParameters = (new TaskDefinition($callable))->rawParameters();

        $resolvedParameters = [];
        $invokedArgs        = $context->arguments();

        foreach ($callableParameters as $index => $parameter) {
            /**
             * @var \ReflectionParameter $parameter
             */
            if (
                $parameter->getClass() &&
                $parameter->getClass()->getName() == TaskContext::class
            ) {
                $resolvedParameters[$index] = $context;
                continue;
            }
            if (
                $parameter->getClass() &&
                $parameter->getClass()->getName() == LoggerInterface::class
            ) {
                $resolvedParameters[$index] = $context->logger();
                continue;
            }

            if (array_key_exists($parameter->name, $invokedArgs)) {
                $resolvedParameters[$index] = $invokedArgs[$parameter->name];
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $resolvedParameters[$index] = $parameter->getDefaultValue();
                continue;
            }

            throw new CannotAutomaticallyInjectParameterException($parameter->name);
        }

        return $resolvedParameters;
    }
}