<?php
namespace KryuuDevelopmentTools;
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    __NAMESPACE__ => array
    (
        'using sass'            => null,
        'public path'           => '../../../../../../public/',
        'tmp path'              => '/cache',
        'tmp id'                => uniqid(),
        'is real public path'   => FALSE,
        'cache timeout'         => 10,
        'caching'               => TRUE,
        'minifying'             => array
        (
            'css'   => TRUE,
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'KryuuDevelopmentTools\Controller\Index' => 'KryuuDevelopmentTools\Controller\IndexController'
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),    
);
