<?php

namespace Mashbo\Mashbot\TaskRunner\Invocation;

use Mashbo\Mashbot\TaskRunner\Exceptions\CannotAutomaticallyInjectParameterException;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Psr\Log\LoggerInterface;

class TaskInvoker
{
    public function invokeCallable(callable $task, TaskContext $context)
    {
        switch (true) {
            case (is_object($task) && ($task instanceof \Closure)):
                $parameters = (new \ReflectionFunction($task))->getParameters();
                break;
            case (is_object($task) && method_exists($task, '__invoke')):
                $parameters = (new \ReflectionClass($task))->getMethod('__invoke')->getParameters();
                break;
            case (is_array($task) && 2 == count($task)):
                $parameters = (new \ReflectionClass($task[0]))->getMethod($task[1])->getParameters();
                break;
            case (is_string($task) && false !== strpos($task, '::')):
                $parts = explode('::', $task);
                $parameters = (new \ReflectionClass($parts[0]))->getMethod($parts[1])->getParameters();
                break;
            default:
                throw new \LogicException("Cannot reflect callable type. This type of callable is not yet supported.");
        }

        return call_user_func_array($task, $this->resolveParametersToCallable($parameters, $context));
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