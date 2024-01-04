<?php

declare(strict_types=1);

namespace ExampleModuleWithUserAndApi\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
// Depending the need for your apps, you can use
//    LdapUser which validate using Active Directory (LDAP)
// or
//    DbUser which would require a database with users
// if you want to use a custom class, make sure it implements
// UserAuth\Model\UserInterface
// for this example, we use the DB version and never check
// anything in a DB, we just return success if a valid JWT was received
use ExampleModuleWithUserAndApi\Model\User as User;
use ExampleModuleWithUserAndApi\Model\Content;

class ApiContentControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        $obj = new $requestName();
        $obj->setUser($container->get(User::class));
        $obj->setContentObj($container->get(Content::class));
        return $obj;
    }
}
