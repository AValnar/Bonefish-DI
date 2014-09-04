<?php
/**
 * Copyright (C) 2014  Alexander Schmidt
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * @author     Alexander Schmidt <mail@story75.com>
 * @copyright  Copyright (c) 2014, Alexander Schmidt
 * @version    1.0
 * @date       2014-07-20
 * @package    Bonefish\DependencyInjection
 */

namespace Bonefish\DependencyInjection;


class Container
{

    /**
     * @var array
     */
    protected $objects = array();

    /**
     * @var array
     */
    protected $alias = array();

    /**
     * Add an object into the container
     *
     * @param string $className
     * @param mixed $obj
     * @throws \Exception
     */
    public function add($className, $obj)
    {
        if ($className == '\Bonefish\DependencyInjection\Container') {
            throw new \Exception('You can not add the Container!');
        }

        if (isset($this->objects[$className])) {
            throw new \Exception('Duplicate entry for key ' . $className);
        }

        $this->objects[$className] = $obj;
    }

    /**
     * Set an alternate name for a class
     *
     * @param string $className
     * @param string $alias
     */
    public function alias($className, $alias)
    {
        $this->alias[$alias] = $className;
    }

    /**
     * Get a singleton and create if needed
     *
     * @param string $className
     * @return mixed
     */

    public function get($className)
    {
        $className = $this->getAliasForClass($className);

        if ($className == '\Bonefish\DependencyInjection\Container') {
            return $this;
        }

        if (!isset($this->objects[$className])) {
            $this->objects[$className] = $this->create($className);
        }

        return $this->objects[$className];
    }

    /**
     * Create a object with dependency injection via annotation
     *
     * @param string $className
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */

    public function create($className, $parameters = array())
    {
        if ($className == '\Bonefish\DependencyInjection\Container') {
            return $this;
        }

        $className = $this->getAliasForClass($className);
        return $this->finalizeObject($className, true, $parameters);
    }

    /**
     * Get alias for a class if one exists
     *
     * @param $className
     * @return string
     */
    protected function getAliasForClass($className)
    {
        if (isset($this->alias[$className])) {
            $className = $this->alias[$className];
        }

        return $className;
    }

    /**
     * Perform lazy dependency injection on object and init object
     *
     * @param mixed $obj
     * @param bool $init
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */
    public function finalizeObject($obj, $init = false, $parameters = array())
    {

        $r = new \ReflectionClass($obj);

        if ($r->isAbstract()) {
            throw new \Exception('Class ' . $obj . ' is Abstract!');
        }

        if ($init) {
            $obj = $r->newInstanceArgs($parameters);
        }

        foreach ($r->getProperties() as $property) {
            $this->processProperty($obj, $property);
        }

        if (method_exists($obj,'__init') && is_callable(array($obj, '__init'))) {
            $obj->__init();
        }

        return $obj;
    }

    /**
     * @param object $obj
     * @param \ReflectionProperty $property
     * @throws \Exception
     */

    protected function processProperty($obj, \ReflectionProperty $property)
    {
        $docComment = $this->cleanPhpDoc($property->getDocComment());
        foreach (explode("\n", $docComment) as $item) {
            if ($this->existsInjectDecorator($item)) {
                if (!preg_match(',@var\s+([^\s]+),', $docComment, $matches)) {
                    throw new \Exception('No @var tag found for property ' . $property->getName() . ' with @inject tag');
                }
                $this->performDependencyInjection($obj, $property, $matches[1]);
            }
        }
    }

    /**
     * Strip phpDoc comment of unneeded characters for DI
     *
     * @param $docComment
     * @return mixed|string
     */

    protected function cleanPhpDoc($docComment)
    {
        $docComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
        $docComment = trim(str_replace('*/', null, $docComment));
        return $docComment;
    }

    /**
     * Check if string contains @inject
     *
     * @param $item
     * @return bool
     */

    protected function existsInjectDecorator($item)
    {
        return strpos($item, '@inject') !== false;
    }

    /**
     * Perform lazy Dependency Injection
     *
     * @param mixed $parent
     * @param \ReflectionProperty $property
     * @param string $className
     */

    protected function performDependencyInjection($parent, \ReflectionProperty $property, $className)
    {
        if ($className == '\Bonefish\DependencyInjection\Container') {
            $value = $this;
        } else {
            $value = new Proxy($className, $property->getName(), $parent, $this);
        }
        $this->injectValueIntoProperty($parent, $property, $value);
    }

    /**
     * Set value of property
     *
     * @param mixed $parent
     * @param \ReflectionProperty $property
     * @param mixed $value
     */

    protected function injectValueIntoProperty($parent, \ReflectionProperty $property, $value)
    {
        $property->setAccessible(true);
        $property->setValue($parent, $value);
    }

    /**
     * Clear all services
     */
    public function tearDown()
    {
        $this->objects = array();
    }

    /**
     * Check if alias is set
     *
     * @param string $alias
     * @return bool
     */
    public function issetAlias($alias)
    {
        return isset($this->alias[$alias]);
    }

    /**
     * Return array of all created services
     *
     * @return array
     */
    public function getSingletons()
    {
        $list = array();

        foreach ($this->objects as $className => $object) {
            $list[] = $className;
        }

        return $list;
    }
} 