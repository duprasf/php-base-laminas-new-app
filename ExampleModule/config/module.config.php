<?php
declare(strict_types=1);

namespace ExampleModule;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Factory\Controller\Plugin\CommonMetadataFactory;


return [
    'router' => [
        'routes' => [
            'root'=> [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '[/]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'splash',
                    ],
                ],
                'may_terminate' => true,
            ],
            __NAMESPACE__ => [
                'type'    => Segment::class,
                'options' => [
                    // this is the /en/ or /fr/ that begins the route
                    'route'    => '/:locale',
                    'defaults' => [
                        'locale'     => 'en',
                    ],
                    'constraints'=>[
                        'locale'=>'en|fr',
                    ],
                ],
                // may_terminate is false since /en/ and /fr/ should go
                // to a home page. If your module is the home page of
                // the server, please change to true
                'may_terminate' => false,
                'child_routes'=>[
                    // this is all the other path of your app
                    'first-page'=>[
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/{my-app}',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'=>[
                        ],
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
        'aliases'=> [
            'exampleModuleCommonMetadata'=>Controller\Plugin\CommonMetadata::class,
        ],
        'factories' => [
            Controller\Plugin\CommonMetadata::class=>CommonMetadataFactory::class,
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
    'translator' => [
        'locale' => 'en_CA',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern'  => '%s.mo',
            ]
        ],
    ],
];
