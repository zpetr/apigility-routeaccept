<?php
namespace zPetr\RouteAccept;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request;
use Laminas\Mvc\MvcEvent;

class RouteAcceptListener extends AbstractListenerAggregate
{
    protected $headerName = 'accept';
    
    protected $regexes = array(
        '#^[a-z]+/vnd\.(?P<zf_ver_vendor>[^.]+)\.v(?P<zf_ver_version>\d+)(?:\.(?P<zf_ver_resource>[a-zA-Z0-9_-]+))?(?:\+[a-z]+)?$#',
    );
	
	/**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events, $priority = -40)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), $priority);
    }

    /**
     * Match against the Content-Type header and inject into the route matches
     *
     * @param MvcEvent $e
     */
    public function onRoute(MvcEvent $e)
    {
        $routeMatches = $e->getRouteMatch();
        if ((!$routeMatches instanceof \Zend\Mvc\Router\RouteMatch) && (!$routeMatches instanceof \Zend\Router\Http\RouteMatch)) {
            return;
        }

        $request = $e->getRequest();
        if (!$request instanceof Request) {
            return;
        }
        
        $headers = $request->getHeaders();
        if (!$headers->has($this->headerName)) {
            return;
        }

        $header = $headers->get($this->headerName);
        
        $matches = $this->parseHeaderForMatches($header->getFieldValue());
        if (is_array($matches)) {
            $this->injectRouteMatches($routeMatches, $matches);
        }
    }
	
	/**
     * Parse the header for matches against registered regexes
     *
     * @param  string $value
     * @return false|array
     */
    protected function parseHeaderForMatches($value)
    {
        $parts = explode(';', $value);
        $contentType = array_shift($parts);
        $contentType = trim($contentType);
    
        foreach (array_reverse($this->regexes) as $regex) {
            if (!preg_match($regex, $contentType, $matches)) {
                continue;
            }
    
            return $matches;
        }
    
        return false;
    }

    /**
     * Inject regex matches into the route matches
     *
     * @param  RouteMatch $routeMatches
     */
    protected function injectRouteMatches($routeMatches, $matches)
    {
        if (!class_exists('\ZF\Apigility\Admin\Module', false)){
			$vendor = $matches['zf_ver_vendor'];
            $version = $matches['zf_ver_version'];
            $controllerTest = $vendor.'\V'.$version;
            if(strpos($routeMatches->getParam('controller'),$controllerTest) !== 0){
                $controllerParts = explode('\\',$routeMatches->getParam('controller'));
                $controllerParts[0] = $vendor;
                $controllerParts[1] = 'V'.$version;
                $controller = implode('\\',$controllerParts);
                $routeMatches->setParam('controller',$controller);
            }
			if(strpos($routeMatches->getMatchedRouteName(),$vendor) !== 0){
                $routeMatchedParts = explode('.',$routeMatches->getMatchedRouteName());
				$routeMatchedParts[0] = $vendor;
                $routeMatches->setMatchedRouteName(implode($routeMatchedParts));
            }
		}
    }
}
