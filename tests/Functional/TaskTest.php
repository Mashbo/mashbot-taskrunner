<?php

namespace Mashbo\Mashbot\TaskRunner\Tests\Functional;

use Mashbo\Mashbot\TaskRunner\Invocation\DirectTaskInvoker;
use Mashbo\Mashbot\TaskRunner\Invocation\TaskInvoker;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use Prophecy\Util\StringUtil;
use Psr\Log\NullLogger;

abstract class TaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var callable
     */
    private $sut;

    /**
     * @var TaskRunner|ObjectProphecy
     */
    protected $runner;

    /**
     * @return callable
     */
    abstract protected function getTask();


    public function setUp()
    {
        $prophet = new Prophet();
        $this->sut = $this->getTask();
        $this->runner = $prophet->prophesize(TaskRunner::class);
        $this->runner->invoke(Argument::cetera())->will(function($args, ObjectProphecy $object, MethodProphecy $method) {

            $expected  = implode("\n", array_map(function (MethodProphecy $methodProphecy) {
                return sprintf('  - %s(%s)',
                    $methodProphecy->getMethodName(),
                    $methodProphecy->getArgumentsWildcard()
                );
            }, call_user_func_array('array_merge', $object->getMethodProphecies())));

            throw new \LogicException("{$method->getMethodName()} with " . (new StringUtil())->stringify($args) . " was not expected, expected calls were: \n" . $expected);
        });
    }

    protected function invoke($args)
    {
        $invoker = new DirectTaskInvoker();
        return $invoker->invokeCallable($this->sut, new TaskContext($this->runner->reveal(), new NullLogger(), $args));
    }
}