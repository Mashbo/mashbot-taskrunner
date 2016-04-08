<?php

namespace Mashbo\Mashbot\TaskRunner\Inspection;

class TaskParameter
{
    /**
     * @var \ReflectionParameter
     */
    private $param;

    private function __construct()
    {
    }

    public static function fromReflectionParameter(\ReflectionParameter $parameter)
    {
        $param = new self;
        $param->param = $parameter;
        return $param;
    }

    public function name()
    {
        return $this->param->name;
    }
}
