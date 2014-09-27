Bonefish-DI
===========
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AValnar/Bonefish-DI/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AValnar/Bonefish-DI/?branch=master)  [![Code Coverage](https://scrutinizer-ci.com/g/AValnar/Bonefish-DI/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AValnar/Bonefish-DI/?branch=master)  [![Build Status](https://scrutinizer-ci.com/g/AValnar/Bonefish-DI/badges/build.png?b=master)](https://scrutinizer-ci.com/g/AValnar/Bonefish-DI/build-status/master)

Bonefish-DI is a dead simple and small Dependency Injection Container.

Features
========
- No outside dependencies
- Inject services with @inject annotation
- Lazy dependency injection by default

Installation
===========
Please use Composer to install this package.
```shell
$ composer require av/bonefish-di:dev-master
```

Usage
=====
Simple creating with injection without adding it into the container
```shell
// Create an Object and inject all Services
$container = new Bonefish\DependencyInjection\Container();
$foo = $container->create('\Some\Random\Class');
// or with parameters
$bar = $container->create('\Some\Random\Class',array('bar','baz'));
```

Create a new service and save it in the container
```shell
// Create a service, no parameters here
$container = new Bonefish\DependencyInjection\Container();
$service = $container->get('\Some\Random\Service');
```

You can also create Objects and add them later to be used as services
```shell
// Create an Object and inject all Services
$container = new Bonefish\DependencyInjection\Container();
$bar = $container->create('\Some\Random\Class',array('bar','baz'));
$container->add('\Some\Random\Class',$bar);
```

You can also define aliases
```shell
// Create an Object and inject all Services
$container = new Bonefish\DependencyInjection\Container();
$service = $container->get('\Some\Random\Service');
$container->alias('Alias','\Some\Random\Service');
// This will now return \Some\Random\Service
$service2 = $container->get('Alias');
```

You can also check if an alias is set, tear down the whole container and list all services in this container.

