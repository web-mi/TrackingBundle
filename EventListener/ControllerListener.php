<?php

namespace WEBMI\TrackingBundle\EventListener;

use Doctrine\Common\Annotations\Reader;//This thing read annotations
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;//Use essential kernel component
use WEBMI\TrackingBundle\Annotations;//Use our annotation

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\DependencyInjection\ContainerInterface;
use WEBMI\TrackingBundle\Entity\Tracking;
use WEBMI\TrackingBundle\Entity\TrackingManagerInterface;
use DateTime;

class ControllerListener 
{
    /**
     * Annotation Reader
     *
     * @var AnnotationReader
     */
    protected $reader;
    
    /**
     * Container
     *
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * Container
     *
     * @var TrackingManagerInterface
     */
    protected $trackingManager;

    /**
     * Listener constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(Reader $reader, ContainerInterface $container, TrackingManagerInterface $trackingManager)
    {
        $this->reader = $reader;
        $this->container = $container;
        $this->trackingManager = $trackingManager;
    }

    /**
     * kernel.request Event
     *
     * @param GetResponseEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) { //return if no controller
            return;
        }

        $request  = $event->getRequest();
        
        $trackDomain = false;
        
        if ($this->container->hasParameter('webmi_tracking')) {
            $trackingConfig = $this->container->getParameter('webmi_tracking');
            $trackingDomains = $trackingConfig['domains'];
            $ignoredTrackingDomains = $trackingConfig['ignored_domains'];
            
            $http_host = $request->getHost();
            if (count($trackingDomains) > 0) {
                foreach($trackingDomains as $trackingDomain) {
                    if (preg_match('/'.$trackingDomain.'/', $http_host)) {
                        $trackDomain = true;
                        break;
                    }
                }
            }
            if (count($ignoredTrackingDomains) > 0) {
                foreach($ignoredTrackingDomains as $ignoredTrackingDomain) {
                    if (preg_match('/'.$ignoredTrackingDomain.'/', $http_host)) {
                        $trackDomain = false;
                        break;
                    }
                }
            }
            if ($trackDomain) {
        
                $object = new \ReflectionObject($controller[0]);// get controller
                $method = $object->getMethod($controller[1]);// get method

                foreach ($this->reader->getMethodAnnotations($method) as $configuration) { //Start of annotations reading
                    if(isset($configuration->type) && $configuration->type != false){//Found our annotation
                        $request  = $event->getRequest();

                        $user = $this->container->get('security.context')->getToken()->getUser();

                        //Request to get Vars from GET-Parameter if needed
                        $getRequest = Request::createFromGlobals();

                        $referrer = $request->headers->get('referer');
                        $targetUrl = "http://".$request->getHost().$request->getRequestUri();
                        $clientIp = $request->getClientIp();

                        $tracking = new Tracking();

                        $tracking->setUserId($user);
                        $tracking->setTrackDate(new DateTime());
                        $tracking->setTrackIp($clientIp);
                        $tracking->setTrackReferrer($referrer);
                        $tracking->setTrackTarget($targetUrl);
                        $tracking->setType($configuration->type);

                        if(isset($configuration->addValues) && $configuration->addValues != false){//Found our annotation
                            $addCount = 1;
                            foreach ($configuration->addValues as $aValue) {
                                if ($aValue != '' && $addCount <= 5) {
                                    $paraKeys = array();
                                    if (preg_match_all('/\{([^\{\}]*)\}/', $aValue, $paraKeys)) {
                                        foreach ($paraKeys[1] as $paraKey) {
                                            $paraValue = $request->get($paraKey);
                                            if ($paraValue == '') {
                                                $paraValue = $getRequest->get($paraKey);
                                                if ($paraValue == '') {
                                                    $paraValue = 'NULL';
                                                }
                                            }
                                            $aValue = preg_replace('/(.*)\{'.$paraKey.'\}(.*)/', '${1}'.$paraValue.'${2}', $aValue);
                                        }
                                        $tracking->setAdd($addCount, $aValue);
                                    } else {
                                        $tracking->setAdd($addCount, $aValue);
                                    }
                                    $addCount++;
                                }
                            }
                        }

                        if(isset($configuration->rest) && $configuration->rest != false){//Found our annotation

                            $restArray = $configuration->rest;
                            foreach ($restArray as $restKey => $restValue) {
                                if ($restValue != '') {
                                    $paraKeys = array();
                                    if (preg_match_all('/\{([^\{\}]*)\}/', $restValue, $paraKeys)) {
                                        foreach ($paraKeys[1] as $paraKey) {
                                            $paraValue = $request->get($paraKey);
                                            if ($paraValue == '') {
                                                $paraValue = $getRequest->get($paraKey);
                                                if ($paraValue == '') {
                                                    $paraValue = 'NULL';
                                                }
                                            }
                                            $restValue = preg_replace('/(.*)\{'.$paraKey.'\}(.*)/', '${1}'.$paraValue.'${2}', $restValue);
                                        }
                                        $restArray[$restKey] = $restValue;
                                    } else {
                                        $restArray[$restKey] = $restValue;
                                    }
                                }
                            }

                            $tracking->setRest($restArray);
                        }

                        $this->trackingManager->saveTracking($tracking);
                    }
                }
            }
        }
    }

}

?>
