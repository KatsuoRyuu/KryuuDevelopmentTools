<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace KryuuDevelopmentTools;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(        
			'Zend\Loader\ClassMapAutoloader' => array(  // THIS IS
				__DIR__ . '/autoload_classmap.php'      // THE PROBABLE
			),     
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getViewHelperConfig() 
    {
        return array(
            'factories' => array(
                'headLink' => function($serviceManager) {
                    $config = $serviceManager->getServiceLocator()->get('config');
                    $helper = new View\Helper\HeadLink();
                    $helper->setConfig($config[__NAMESPACE__]);
                    //var_dump($config[__NAMESPACE__]);
                    return $helper;
                }
            )
        );
    }
}
