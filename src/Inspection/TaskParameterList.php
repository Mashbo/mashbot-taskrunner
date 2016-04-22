<?php

namespace Mashbo\Mashbot\TaskRunner\Inspection;

use Traversable;

final class TaskParameterList implements \IteratorAggregate
{
    /**
     * @var \ReflectionParameter[]
     */
    private $params = [];

    private function __construct() {}

    private function add(\ReflectionParameter $parameter)
    {
        $this->params[$parameter->getName()] = TaskParameter::fromReflectionParameter($parameter);
    }

    /**
     * @param \ReflectionParameter[] $parameters
     * @return TaskParameterList
     */
    public static function fromReflectionParameterArray(array $parameters)
    {
        $list = new self;
        foreach ($parameters as $param) {
            $list->add($param);
        }

        return $list;
    }

    /**
     * @return \ArrayIterator|TaskParameter[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->params);
    }
}
