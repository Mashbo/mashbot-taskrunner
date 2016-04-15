<?php

namespace Mashbo\Mashbot\TaskRunner;

use Mashbo\Mashbot\TaskRunner\Exceptions\ArgumentNotSetException;
use Psr\Log\LoggerInterface;

class TaskContext
{
    /**
     * @var TaskRunner
     */
    private $taskRunner;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $arguments;

    public function __construct(TaskRunner $taskRunner, LoggerInterface $logger, $arguments)
    {
        $this->taskRunner = $taskRunner;
        $this->logger     = $logger;
        $this->arguments  = $arguments;
    }

    public function logger()
    {
        return $this->logger;
    }

    public function taskRunner()
    {
        return $this->taskRunner;
    }

    public function arguments()
    {
        return $this->arguments;
    }

    public function argument($name)
    {
        if (!array_key_exists($name, $this->arguments)) {
            throw new ArgumentNotSetException($name, $this->arguments);
        }
        return $this->arguments[$name];
    }
}