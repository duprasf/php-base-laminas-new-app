<?php

declare(strict_types=1);

namespace ExampleModuleWithUserAndApi\Factory\Model;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ExampleModuleWithUserAndApi\Model\User;

class ContentFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        $obj = new $requestName();

        $user = $container->get(User::class);
        $obj->setUser($user);

        // This example-module-with-user-and-api-pdo is taken from the $container,
        // the $container objects is defined using the configuration files
        $pdoServiceName = 'example-module-with-user-and-api-pdo';
        // if the PDO config name exists and there is a setDB function...
        if($container->has($pdoServiceName) && method_exists($obj, 'setDb')) {
            // ... then the PDO will be sent to the object
            $obj->setDb($container->get($pdoServiceName));
        }


        // return the object
        return $obj;
    }
}
