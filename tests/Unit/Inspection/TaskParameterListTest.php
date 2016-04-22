<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Unit\Inspection;

use Mashbo\Mashbot\TaskRunner\Inspection\TaskParameterList;

class TaskParameterListTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_is_iterable_even_when_empty_args_passed()
    {
        $sut = TaskParameterList::fromReflectionParameterArray([]);
        $this->assertCount(0, $sut->getIterator());
    }
}