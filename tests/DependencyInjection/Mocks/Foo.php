<?php
/**
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 * @author     Alexander Schmidt <mail@story75.com>
 * @copyright  Copyright (c) 2014, Alexander Schmidt
 * @version    1.0
 * @date       2014-07-20
 * @package    Bonefish
 * @subpackage Tests\DependencyInjection\Mocks
 */

namespace Bonefish\Tests\DependencyInjection\Mocks;


class Foo {

    /**
     * @var bool
     */
    public $initCalled = false;

    /**
     * @var \stdClass
     */
    public $publicPropertyNoInject = false;

    /**
     * @var \stdClass
     * @inject
     */
    public $publicPropertyWithInject = false;

    /**
     * @var \stdClass
     * @inject
     */
    public $protectedPropertyWithInject;

    /**
     * @var \Bonefish\DependencyInjection\Container
     * @inject
     */
    public $container;

    public function __init()
    {
        $this->initCalled = true;
    }

    public function getProtectedPropertyWithInject()
    {
        return $this->protectedPropertyWithInject;
    }
} 