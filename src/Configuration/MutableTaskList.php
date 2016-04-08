<?php

namespace Mashbo\Mashbot\TaskRunner\Configuration;

use Mashbo\Mashbot\TaskRunner\Configuration\Exceptions\UndefinedTaskException;

class MutableTaskList implements \Countable
{
    /**
     * @var array
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = [];
    }

    public function find($task)
    {
        if (!array_key_exists($task, $this->tasks)) {
            throw new UndefinedTaskException($task);
        }
        return $this->tasks[$task];
    }

    public function add($name, callable $task)
    {
        $this->tasks[$name] = $task;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->tasks);
    }
}
