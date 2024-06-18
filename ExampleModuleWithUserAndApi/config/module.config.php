<?php
declare(strict_types=1);

namespace ExampleModuleWithUserAndApi;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use UserAuth\Factory\GetJwtDataFactory;
use Application\Factory\Controller\Plugin\CommonMetadataFactory;


return [
    'router' => [
        'routes' => [
            __NAMESPACE__ => [
                'type'    => Segment::class,
                'options' => [
                    // this is the /en/ or /fr/ that begins the route
                    'route'    => '/[:locale]',
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
                            'route'    => '/{my-app-with-user}',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'index',
                                'token'      => '',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'=>[
                            'api'=>[
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/api/v1',
                                    'defaults' => [
                                        'action'     => null,
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'=>[
                                    'user'=>[
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/{user}',
                                            'defaults' => [
                                                'controller' => Controller\ApiUserController::class,
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'=>[
                                            'register'=>[
                                                'type'    => Segment::class,
                                                'options' => [
                                                    'route'    => '/register',
                                                    'defaults' => [
                                                        'type'=>'register',
                                                    ],
                                                ],
                                                'may_terminate' => true,
                                                'child_routes'=>[
                                                ],
                                            ],
                                        ],
                                    ],
                                    'user-ldap'=>[
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/user-ldap',
                                            'defaults' => [
                                                'controller' => Controller\ApiUserLdapController::class,
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'=>[
                                        ],
                                    ],
                                    'content'=>[
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/{content}',
                                            'defaults' => [
                                                'controller' => Controller\ApiContentController::class,
                                            ],
                                        ],
                                        'may_terminate' => true,
                                        'child_routes'=>[
                                        ],
                                    ],
                                ],
                            ],
                            'content'=>[
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/{content}',
                                    'defaults' => [
                                        'controller' => Controller\WithSessionController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'=>[
                                ],
                            ],
                            'oauth-return'=>[
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/{return}',
                                    'defaults' => [
                                        'controller' => Controller\IndexController::class,
                                        'action'     => 'return',
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
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Factory\Controller\IndexControllerFactory::class,
            Controller\WithSessionController::class => Factory\Controller\WithSessionControllerFactory::class,
            Controller\ApiUserController::class => Factory\Controller\ApiUserControllerFactory::class,
            Controller\ApiUserLdapController::class => Factory\Controller\ApiUserLdapControllerFactory::class,
            Controller\ApiContentController::class => Factory\Controller\ApiContentControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'invokables'=>[
            'setResponseHeaders' => Controller\Plugin\SetResponseHeaders::class,
            'returnUserData' => Controller\Plugin\ReturnUserData::class,
        ],
        'factories' => [
            'getJwtData' => GetJwtDataFactory::class,
            Controller\Plugin\CommonMetadata::class=>CommonMetadataFactory::class,
        ],
        'aliases' => [
            'exampleModuleWithUserAndApiCommonMetadata'=>Controller\Plugin\CommonMetadata::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Model\Model::class => Factory\Model\ModelFactory::class,
            Model\User::class => Factory\Model\UserFactory::class,
            Model\UserLdap::class => Factory\Model\UserFactory::class,
            Model\Content::class => Factory\Model\ContentFactory::class,
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
