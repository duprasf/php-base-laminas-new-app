<?php
namespace ExampleModuleWithUserAndApi\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class SetResponseHeaders extends AbstractPlugin
{
    /**
    * Header sent to each request
    *
    * @param mixed $response
    * @param mixed $domain, which domain can use this API, by default all domain can use it
    * @param mixed $maxAge, how long before the browser will refresh these settings, by default 20 days
    */
    public function __invoke($response, $domain = '*', int $maxAge = 1728000)
    {
        if(!is_int($maxAge)) {
            // by default, I chose 20 days
            $maxAge = 1728000;
        }
        $response->getHeaders()
            // *, <origin> or https://domain.com
            ->addHeaderLine('Access-Control-Allow-Origin', $domain)
            // GET, POST, PUT, PATCH, PUSH, DELETE, HEAD, OPTIONS
            ->addHeaderLine('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->addHeaderLine('Access-Control-Allow-Headers', 'Authorization, Content-Type, x-access-token')
            ->addHeaderLine('Access-Control-Allow-Credentials', 'true')
            // good for the specified number of seconds
            ->addHeaderLine('Access-Control-Max-Age', $maxAge)

            //->addHeaderLine('Content-Type','application/json; charset=utf-8')
        ;

        return $response;
   }
}
