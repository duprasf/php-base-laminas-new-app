<?php
namespace ExampleModule;

return [
    'gc-notify-config'=>[
        __NAMESPACE__=>[
            'appName'=>__NAMESPACE__,
            'apikey'=>'API key provided by GC Notify',
            'templates'=>[
                'email1'=>'templateKeyFromGCNotify',
                'email2'=>'templateKeyFromGCNotify',
                'email3'=>'templateKeyFromGCNotify',
            ],
        ],
    ],
];
