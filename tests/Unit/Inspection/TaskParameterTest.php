<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit\Inspection;

use Mashbo\Mashbot\TaskRunner\Inspection\TaskParameter;

class TaskParameterTest extends \PHPUnit_Framework_TestCase
{
    public function test_exposes_name()
    {
        $sut = TaskParameter::fromReflectionParameter($this->parameterNamedA());
        $this->assertEquals('a', $sut->name());
        $this->assertTrue($sut->isRequired());
    }

    public function test_is_not_required_when_a_null_default_is_set()
    {
        $sut = TaskParameter::fromReflectionParameter($this->parameterWhichDefaultsToNull());
        $this->assertFalse($sut->isRequired());
        $this->assertNull($sut->defaultValue());
    }

    public function test_is_not_required_when_a_string_default_is_set()
    {
        $sut = TaskParameter::fromReflectionParameter($this->parameterWhichDefaultsToString());
        $this->assertFalse($sut->isRequired());
        $this->assertEquals('string', $sut->defaultValue());
    }

    /**
     * @return \ReflectionParameter
     */
    private function parameterNamedA()
    {
        return (new \ReflectionFunction(function ($a) {
        }))->getParameters()[0];
    }

    /**
     * @return \ReflectionParameter
     */
    private function parameterWhichDefaultsToNull()
    {
        return (new \ReflectionFunction(function ($a = null) {
        }))->getParameters()[0];
    }

    /**
     * @return \ReflectionParameter
     */
    private function parameterWhichDefaultsToString()
    {
        return (new \ReflectionFunction(function ($a = 'string') {
        }))->getParameters()[0];
    }
}