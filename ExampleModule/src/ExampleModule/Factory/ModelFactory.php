<?php

declare(strict_types=1);

namespace ExampleModule\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

use ExampleModule\Model\Model;

class ModelFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        // The factory created the object to be returned
        $obj = new Model();

        // This PDO Example is taken from the container, the container
        // objects is defined in the configuration files
        $pdoServiceName = 'pdoExample';
        // if the PDO config name exists and there is a setDB function,
        if($container->has($pdoServiceName) && method_exists($obj, 'setDb')) {
            // then the PDO will be sent to the object
            $obj->setDb($container->get($pdoServiceName));
        }


        // return the object
        return $obj;
    }
}
