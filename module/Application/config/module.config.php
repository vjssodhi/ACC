<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

return array (
		'user_module_op_config' => array (
				'restricted_access_handler' => 'Application\View\RestrictedAccessHandler',
				'templates' => array (
						'access_denied' => 'error/accessdenied' 
				) 
		),
		'doctrine' => array (
				'driver' => array (
						'application_entities' => array (
								'class' => '\Doctrine\ORM\Mapping\Driver\AnnotationDriver',
								'cache' => 'array',
								'paths' => array (
										realpath ( __DIR__ . '/../src/Application/Entity' ) 
								) 
						),
						'orm_default' => array (
								'class' => 'Doctrine\ORM\Mapping\Driver\DriverChain',
								'drivers' => array (
										'Application\Entity' => 'application_entities' 
								) 
						) 
				) 
		),
		'router' => array (
				'routes' => array (
						'anonymous' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/anonymous',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Anonymous',
												'action' => 'index' 
										) 
								),
								'may_terminate' => true,
								'child_routes' => array (
										'signin' => array (
												'type' => 'Literal',
												'options' => array (
														'route' => '/signin',
														'defaults' => array (
																'action' => 'signin' 
														) 
												) 
										),
										'signup' => array (
												'type' => 'Literal',
												'options' => array (
														'route' => '/signup',
														'defaults' => array (
																'action' => 'signup' 
														) 
												) 
										) 
								) 
						),
						'home' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/home',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Index',
												'action' => 'index' 
										) 
								) 
						),
						'base' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Index',
												'action' => 'index' 
										) 
								) 
						),
						'user' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/user',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'User',
												'action' => 'index' 
										) 
								),
								
								'may_terminate' => true,
								'child_routes' => array (
										'signout' => array (
												'type' => 'Literal',
												'options' => array (
														'route' => '/signout',
														'defaults' => array (
																'action' => 'signout' 
														) 
												) 
										),
										'dashboard' => array (
												'type' => 'Literal',
												'options' => array (
														'route' => '/dashboard',
														'defaults' => array (
																'action' => 'dashboard' 
														) 
												) 
										) 
								) 
						),
						'report' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/report',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Report',
												'action' => 'dashboard' 
										) 
								),
								
								'may_terminate' => true,
								'child_routes' => array (
										'evaluationform' => array (
												'type' => 'Segment',
												'options' => array (
														'route' => '/evaluationform/:userId',
														'constraints' => array (
																'userId' => '[0-9]+' 
														),
														'defaults' => array (
																'action' => 'evaluationform' 
														) 
												) 
										) 
								) 
						),
						'admin' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/admin',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Admin',
												'action' => 'index' 
										) 
								),
								
								'may_terminate' => true,
								'child_routes' => array (
										'adduser' => array (
												'type' => 'Literal',
												'options' => array (
														'route' => '/adduser',
														'defaults' => array (
																'action' => 'adduser' 
														) 
												) 
										),
										'removeuser' => array (
												'type' => 'Literal',
												'options' => array (
														'route' => '/removeuser',
														'defaults' => array (
																'action' => 'removeuser' 
														) 
												) 
										),
										'listusers' => array (
												'type' => 'Literal',
												'options' => array (
														'route' => '/listusers',
														'defaults' => array (
																'action' => 'listusers' 
														) 
												) 
										),
										'updateuser' => array (
												'type' => 'Segment',
												'options' => array (
														'route' => '/updateuser/:userId',
														'constraints' => array (
																'userId' => '[0-9]+' 
														),
														'defaults' => array (
																'action' => 'updateuser' 
														) 
												) 
										),
										'updateusers' => array (
												'type' => 'Literal',
												'options' => array (
														'route' => '/updateusers',
														'defaults' => array (
																'action' => 'updateusers' 
														) 
												) 
										) 
								) 
						) 
				) 
		),
		'service_manager' => array (
				'abstract_factories' => array (
						'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
						'Zend\Log\LoggerAbstractServiceFactory' 
				),
				'factories' => array (
						'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory' 
				) 
		),
		'translator' => array (
				'locale' => 'en_US',
				'translation_file_patterns' => array (
						array (
								'type' => 'gettext',
								'base_dir' => __DIR__ . '/../language',
								'pattern' => '%s.mo' 
						) 
				) 
		),
		'controllers' => array (
				'invokables' => array (
						'Application\Controller\Index' => Controller\IndexController::class,
						'Application\Controller\User' => Controller\UserController::class,
						'Application\Controller\Anonymous' => Controller\AnonymousController::class,
						'Application\Controller\Admin' => Controller\AdminController::class,
						'Application\Controller\Report' => Controller\ReportController::class 
				) 
		),
		'view_manager' => array (
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => array (
						'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
						'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		),
		// Placeholder for console routes
		'console' => array (
				'router' => array (
						'routes' => array () 
				) 
		) 
);
