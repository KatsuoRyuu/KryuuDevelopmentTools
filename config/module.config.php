<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
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
	'view_helpers' => array(
        'invokables' => array(
            'headLink'				=> 'KryuuDevelopmentTools\View\Helper\HeadLink',
            //'viewAllBlogPosts'  => 'KryuuSimpleMessage\View\Helper\ViewAllPostsHelper',
        ),
    ),
);
