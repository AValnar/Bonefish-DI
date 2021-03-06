<?php
/**
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 * @author     Alexander Schmidt <mail@story75.com>
 * @copyright  Copyright (c) 2014, Alexander Schmidt
 * @version    1.0
 * @date       2014-05-02
 * @package    Bonefish
 * @subpackage Tests\DependencyInjection
 */

namespace Bonefish\Tests\DependencyInjection;


use Bonefish\DependencyInjection\Proxy;

class ProxyTest extends \PHPUnit_Framework_TestCase
{

    public function testProxyReplace()
    {
        $parent = new \stdClass();
        $parent->test = 'bar';

        $container = $this->getMockBuilder('\Bonefish\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $dependency = $this->getMockBuilder('\stdClass')
            ->disableOriginalConstructor()
            ->setMethods(array('test'))
            ->getMock();

        $dependency->expects($this->once())
            ->method('test')
            ->will($this->returnValue('foo'));

        $container->expects($this->once())
            ->method('get')
            ->will($this->returnValue($dependency));

        $r = new \ReflectionObject($parent);
        $property = $r->getProperty('test');

        $parent->test = new Proxy('stdClass', $property, $parent, $container);

        $this->assertEquals('foo', $parent->test->test());
        $this->assertEquals(serialize($dependency), serialize($parent->test));
    }

    public function testSerialize()
    {
        $container = $this->getMockBuilder('\Bonefish\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $proxy = new Proxy('stdClass', 'test', new \stdClass(), $container);
        $this->assertEquals(serialize($proxy), 'O:34:"Bonefish\DependencyInjection\Proxy":1:{s:12:" * className";s:8:"stdClass";}');
    }
}
 