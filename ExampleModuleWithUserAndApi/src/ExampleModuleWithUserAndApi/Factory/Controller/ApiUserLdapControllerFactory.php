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
use ExampleModuleWithUserAndApi\Model\UserLdap as User;

class ApiUserLdapControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        $obj = new $requestName();
        $obj->setUser($container->get(User::class));
        return $obj;
    }
}
