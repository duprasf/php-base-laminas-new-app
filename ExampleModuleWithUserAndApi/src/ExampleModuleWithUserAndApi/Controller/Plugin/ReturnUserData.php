<?php

namespace ExampleModuleWithUserAndApi\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\View\Model\JsonModel;
use UserAuth\Model\UserInterface;

class ReturnUserData extends AbstractPlugin
{
    /**
    * Return a simple JsonModel with the JWT and if the user click remember
    * In most app, if the user wants to be remembered, the JWT should be saved in
    * localStorage, if they do not want to be remembered, the JWT should be
    * saved in localSession
    *
    * @param UserInterface $user
    * @param int $length, the number of seconds the JWT will be valid
    * @param bool $remember, see above
    * @return \Laminas\View\Model\JsonModel
    */
    public function __invoke(UserInterface $user, int $length = 86400, bool $remember = null)
    {
        // 2419200 = 28 days, 86400 = 24 hours
        return new JsonModel([
            'remember' => $remember,
            'jwt' => $user->getJWT($length),
        ]);
    }
}
