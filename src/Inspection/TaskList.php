<?php

namespace Mashbo\Mashbot\TaskRunner\Inspection;

use Mashbo\Mashbot\TaskRunner\Configuration\MutableTaskList;

class TaskList implements \Countable
{
    /**
     * @var MutableTaskList
     */
    private $list;

    public function __construct(MutableTaskList $list)
    {
        $this->list = $list;
    }

    public function find($task)
    {
        return new TaskDefinition($this->list->find($task));
    }

    public function count()
    {
        return $this->list->count();
    }
}
