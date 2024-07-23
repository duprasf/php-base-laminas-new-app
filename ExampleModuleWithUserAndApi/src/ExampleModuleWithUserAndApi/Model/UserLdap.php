<?php

namespace ExampleModuleWithUserAndApi\Model;

use UserAuth\Module as UserAuth;
use UserAuth\Model\LdapUser;

class UserLdap extends LdapUser
{
    /**
    * Authenticate/login a user
    *
    * @param string $email depending on your implementation, this could be email, username, etc.
    * @param string $password
    * @return bool
    */
    public function authenticate(String $email, String $password): bool
    {
        // this class is not needed if you do not have any special operations to add
        // the parent method will check the AD setup using the configuration 'ldap-options'
        // it should be an array of array of LDAP connection like this:
        //  'ldap-options' => array(
        //      '1'=>array(
        //          'host'     => 'HCQCK1AWVDCP001.ad.hc-sc.gc.ca',
        //          'username' => 'accountName', // service account if using one
        //          'password' => '**** password ****',
        //          'baseDn'   => 'OU=User Accounts,OU=Accounts,OU=Health Canada,DC=ad,DC=hc-sc,DC=gc,DC=ca',
        //      ),
        //  )
        return parent::authenticate($email, $password);
    }

    /**
    * This function is called when trying to get the JWT
    *
    * @param int $time, the length of time the JWT will be valid. It should not change anything, but just in case...
    * @return array, the data you want to send to client as part of the JWT
    */
    public function getDataForJWT(int $time = 86400): array
    {

        $payload = parent::getDataForJWT($time);
        // here you can add or remove any fields you want to send to the client
        $payload['debug'] = 'debug string';
        $payload['type'] = 'ldap';

        // you must return an array, even an empty array would work, but would be completely useless
        return $payload;
    }

}
