<?php

namespace WEBMI\TrackingBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WEBMI\TrackingBundle\Entity\Tracking;
use WEBMI\TrackingBundle\Entity\TrackingManagerInterface;
use DateTime;

class RequestListener 
{
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
     * siteManager
     *
     * @var siteManager
     */
    protected $siteManager;
    
    /**
     * pageManager
     *
     * @var pageManager
     */
    protected $pageManager;
    
    /**
     * Listener constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, TrackingManagerInterface $trackingManager, $siteManager = null, $pageManager = null)
    {
        $this->container = $container;
        $this->trackingManager = $trackingManager;
        $this->siteManager = $siteManager;
        $this->pageManager = $pageManager;
    }

    /**
     * kernel.request Event
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
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
        
                //$user = $this->container->get('security.context')->getToken()->getUser();
                $sToken = $this->container->get('security.context')->getToken();
                if ($sToken) {
                    $user = $sToken->getUser();
                }


                $request = $event->getRequest();

                $referrer = $request->headers->get('referer');
                $targetUrl = "http://".$request->getHost().$request->getRequestUri();
                $clientIp = $request->getClientIp();

                $tracking = new Tracking();

                $tracking->setUserId($user);
                $tracking->setTrackDate(new DateTime());
                $tracking->setTrackIp($clientIp);
                $tracking->setTrackReferrer($referrer);
                $tracking->setTrackTarget($targetUrl);

                $tracking->setType('request');
                if($this->siteManager) {
                    $site = $this->siteManager->findOneBy(array('host' => $request->getHost()));
                    if ($site && $this->pageManager) {
                        $page = $this->pageManager->findOneBy(array(
                                                                'url' => $request->getPathInfo(),
                                                                'site' => $site->getId()
                                                            ));
                        if ($page) {
                            $tracking->setType($page->getName());
                        }
                    }
                }

                $this->trackingManager->saveTracking($tracking);
            }
        }
    }

}

?>
