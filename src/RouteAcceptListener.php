<?php
namespace zPetr\RouteAccept;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class RouteAcceptListener extends AbstractListenerAggregate
{
    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -50);
    }

    /**
     * Match against the Content-Type header and inject into the route matches
     *
     * @param MvcEvent $e
     */
    public function onRoute(MvcEvent $e)
    {
        $routeMatches = $e->getRouteMatch();
        if (!$routeMatches instanceof RouteMatch) {
            return;
        }

        $request = $e->getRequest();
        if (!$request instanceof Request) {
            return;
        }
        if($routeMatches->getParam('zf_ver_vendor') && $routeMatches->getParam('zf_ver_version')){
            $this->injectRouteMatches($routeMatches);
        }
    }

    /**
     * Inject regex matches into the route matches
     *
     * @param  RouteMatch $routeMatches
     */
    protected function injectRouteMatches(RouteMatch $routeMatches)
    {
        if (!class_exists('\ZF\Apigility\Admin\Module', false)){
			$controllerTest = $routeMatches->getParam('zf_ver_vendor').'\V'.$routeMatches->getParam('zf_ver_version');
			if(strpos($routeMatches->getParam('controller'),$controllerTest) !== 0){
				$controllerParts = explode('\\',$routeMatches->getParam('controller'));
				$controllerParts[0] = $routeMatches->getParam('zf_ver_vendor');
				$controllerParts[1] = 'V'.$routeMatches->getParam('zf_ver_version');
				$controller = implode('\\',$controllerParts);
				$routeMatches->setParam('controller',$controller);
			}
		}
    }
}
