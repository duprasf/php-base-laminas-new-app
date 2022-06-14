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
        /**
        * This PDO Example is taken from the container, the container
        * objects is defined in the configuration files
        */
        $pdo = $container->get('pdoExample');

        // The factory created the object to be returned and will set
        // all the configuration required
        $obj = new Model();
        $obj->setDb($pdo);

        // return the object
        return $obj;
    }
}
