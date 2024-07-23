<?php

declare(strict_types=1);

namespace ExampleModuleWithUserAndApi\Factory\Model;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuth\Factory\User\UserFactory as ParentFactory;
use UserAuth\Factory\User\User;
use UserAuth\Model\User\Storage\MySQLStorage;
use UserAuth\Model\User\Storage\MongodbStorage;
use UserAuth\Model\User\Storage\LdapStorage;
use UserAuth\Model\User\Storage\FileStorage;
use UserAuth\Model\User\Authenticator\CredentialsAuthenticator;
use UserAuth\Model\User\Authenticator\EmailAuthenticator;
use UserAuth\Model\User\Authenticator\LdapAuthenticator;

class UserFactory extends ParentFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        $obj = parent::__invoke($container, User::class, $options);

        // since this value repeats in User and in storage,
        // it might be better to have it in environment variable.
        $idField = 'email';
        $obj->setIdField($idField);

        if(strpos($requestName, "UserLdap") !== false) {
            $obj->setStorage($container->get(LdapStorage::class));
            $obj->setAuthenticator($container->get(LdapAuthenticator::class));
            return $obj;
        }


        // MySQL ******************************************************************
        //$storage = $container->get(MySQLStorage::class);

        // Mongo ******************************************************************
        //$storage = $container->get(MongodbStorage::class);

        // LDAP ******************************************************************
        //$storage = $container->get(LdapStorage::class);

        // File System ******************************************************************
        $storage = $container->get(FileStorage::class);
        // if you are using only a file on the system, you could create a factory,
        // but since it's only two settings, you can also set it in the UserFactory:
        $storage->setFilename('/var/www/data/users.json');
        // set the field name of the unique field of your table (ex: email, username, etc.)
        $storage->setIdField($idField);


        //*****************************************
        // The authenticator is the method a user will use to identify himself.
        // **** CredentialsAuthenticator will use a ID and a password
        $authenticator = $container->get(CredentialsAuthenticator::class);
        // **** EmailAuthenticator will send an email with a link to verify identity
        //$authenticator = $container->get(EmailAuthenticator::class);

        // **** LdapStorage only works with LdapAuthenticator and vise versa
        //$authenticator = $container->get(LdapAuthenticator::class);

        // Inject the Storage and Authenticator to the user
        $obj->setStorage($storage);
        $obj->setAuthenticator($authenticator);
        return $obj;
    }
}
