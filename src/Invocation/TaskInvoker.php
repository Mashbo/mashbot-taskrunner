<?php

namespace Mashbo\Mashbot\TaskRunner\Invocation;

use Mashbo\Mashbot\TaskRunner\Exceptions\CannotAutomaticallyInjectParameterException;
use Mashbo\Mashbot\TaskRunner\Inspection\TaskDefinition;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Psr\Log\LoggerInterface;

class TaskInvoker
{
    public function invokeCallable(callable $task, TaskContext $context)
    {
        return call_user_func_array($task, $this->resolveParametersToCallable(
            (new TaskDefinition($task))->rawParameters(),
            $context)
        );
    }

    private function resolveParametersToCallable($callableParameters, TaskContext $context)
    {
        $resolvedCallableParameters = [];
        $invokedArgs = $context->arguments();

        foreach ($callableParameters as $index => $parameter) {
            /**
             * @var \ReflectionParameter $parameter
             */
            if (
                $parameter->getClass() &&
                $parameter->getClass()->getName() == TaskContext::class
            ) {
                $resolvedCallableParameters[$index] = $context;
                continue;
            }
            if (
                $parameter->getClass() &&
                $parameter->getClass()->getName() == LoggerInterface::class
            ) {
                $resolvedCallableParameters[$index] = $context->logger();
                continue;
            }

            if (array_key_exists($parameter->name, $invokedArgs)) {
                $resolvedCallableParameters[$index] = $invokedArgs[$parameter->name];
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $resolvedCallableParameters[$index] = $parameter->getDefaultValue();
                continue;
            }

            throw new CannotAutomaticallyInjectParameterException($parameter->name);
        }

        return $resolvedCallableParameters;
    }
}