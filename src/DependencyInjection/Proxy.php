<?php
/**
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 * @author     Alexander Schmidt <mail@story75.com>
 * @copyright  Copyright (c) 2014, Alexander Schmidt
 * @version    1.0
 * @date       2014-07-20
 * @package    Bonefish
 * @subpackage DependencyInjection
 */

namespace Bonefish\DependencyInjection;


class Proxy
{

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $property;

    /**
     * @var mixed
     */
    protected $parent;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param string $className
     * @param string $property
     * @param mixed $parent
     * @param Container $container
     */
    public function __construct($className, $property, $parent, $container)
    {
        $this->className = $className;
        $this->property = $property;
        $this->parent = $parent;
        $this->container = $container;
    }

    /**
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments = array())
    {
        $dependency = $this->container->get($this->className);
        $this->parent->{$this->property} = $dependency;
        return call_user_func(array($this->parent->{$this->property},$name),$arguments);
    }

    public function __sleep()
    {
        return array('className');
    }

} 