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

    public function isRequired()
    {
        return !$this->param->isOptional();
    }

    public function defaultValue()
    {
        return $this->param->getDefaultValue();
    }

    public function name()
    {
        return $this->param->name;
    }
}
