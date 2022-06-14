<?php
declare(strict_types=1);

namespace ExampleModule;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'root' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:locale]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                        'locale'     => 'en',
                    ],
                    'constraints'=>[
                        'locale'=>'en|fr',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Factory\IndexControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
        ],
        'aliases' => [
        ],
    ],
    'service_manager' => [
        'factories' => [
            Model\Model::class => Factory\ModelFactory::class,
        ],
        'invokables' => [
        ],
    ],
    'view_helpers' => [
        'invokables' => [
        ],
        'factories' => [
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __NAMESPACE__ => __DIR__ . '/../view',
        ],
    ],
];
