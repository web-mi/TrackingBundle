<?php

/*
 * This file is part of the WEBMITrackingBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WEBMI\TrackingBundle\Security;

use WEBMI\TrackingBundle\Entity\TrackingManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use WEBMI\TrackingBundle\Entity\Tracking;
use DateTime;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InteractiveLoginListener
{
    /**
     * Container
     *
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * trackingManager
     *
     * @var TrackingManagerInterface
     */
    protected $trackingManager;

    public function __construct(TrackingManagerInterface $trackingManager, ContainerInterface $container)
    {
        $this->trackingManager = $trackingManager;
        $this->container = $container;
    }

    /**
     * Event fired if the Login Success
     * @param InteractiveLoginEvent $event 
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $trackDomain = false;
        
        if ($this->container->hasParameter('webmi_tracking')) {
            $trackingConfig = $this->container->getParameter('webmi_tracking');
            $trackingDomains = $trackingConfig['domains'];
            $ignoredTrackingDomains = $trackingConfig['ignored_domains'];

            $request = $event->getRequest();
            /* @var $request \Symfony\Component\HttpFoundation\Request */
            
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
        
                $user = $event->getAuthenticationToken()->getUser();

                $referrer = $request->headers->get('referer');
                $targetUrl = "http://".$request->getHost().$request->getRequestUri();
                $clientIp = $request->getClientIp();

                if ($user instanceof UserInterface) {
                    $tracking = new Tracking();
                    $tracking->setUserId($user);

                    $tracking->setTrackDate(new DateTime());
                    $tracking->setTrackIp($clientIp);
                    $tracking->setTrackReferrer($referrer);
                    $tracking->setTrackTarget($targetUrl);

                    $tracking->setType('login');
                    $this->trackingManager->saveTracking($tracking);
                }
            }
        }
    }
    
    /**
     * Event fired if the Login Success / only available at Symfony 2.1
     * @param InteractiveLoginEvent $event 
     */
    public function onSecurityInteractiveLoginSuccess(AuthenticationEvent $event)
    {
        $trackDomain = false;
        
        if ($this->container->hasParameter('webmi_tracking')) {
            $trackingConfig = $this->container->getParameter('webmi_tracking');
            $trackingDomains = $trackingConfig['domains'];
            $ignoredTrackingDomains = $trackingConfig['ignored_domains'];

            $request = $this->container->get('request');
            /* @var $request \Symfony\Component\HttpFoundation\Request */
            
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
        
                $authToken = $event->getAuthenticationToken();
                if ($authToken) {
                    $user = $authToken->getUser();
                }

                $referrer = $request->headers->get('referer');
                $targetUrl = "http://".$request->getHost().$request->getRequestUri();
                $clientIp = $request->getClientIp();

                $tracking = new Tracking();

                if ($user instanceof UserInterface) {
                    $tracking->setUserId($user);
                }

                $tracking->setTrackDate(new DateTime());
                $tracking->setTrackIp($clientIp);
                $tracking->setTrackReferrer($referrer);
                $tracking->setTrackTarget($targetUrl);

                $tracking->setAdd1('success');

                $tracking->setType('login');

                $this->trackingManager->saveTracking($tracking);
            }
        }
    }
    
    /**
     * Event fired if the Login Failure
     * @param InteractiveLoginEvent $event 
     */
    public function onSecurityInteractiveLoginFailure(AuthenticationFailureEvent $event)
    {
        $trackDomain = false;
        
        if ($this->container->hasParameter('webmi_tracking')) {
            $trackingConfig = $this->container->getParameter('webmi_tracking');
            $trackingDomains = $trackingConfig['domains'];
            $ignoredTrackingDomains = $trackingConfig['ignored_domains'];

            $request = $this->container->get('request');
            /* @var $request \Symfony\Component\HttpFoundation\Request */
            
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

                $authToken = $event->getAuthenticationToken();
                if ($authToken) {
                    $user = $authToken->getUser();
                }

                
                $session = $request->getSession();
                /* @var $session \Symfony\Component\HttpFoundation\Session */

                // last username entered by the user
                $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);


                $referrer = $request->headers->get('referer');
                $targetUrl = "http://".$http_host.$request->getRequestUri();
                $clientIp = $request->getClientIp();

                $tracking = new Tracking();

                if ($user instanceof UserInterface) {
                    $tracking->setUserId($user);
                } elseif ($lastUsername) {
                    $user = $this->container->get('sonata.user.admin.user');
                    $userManager = $user->getUserManager();
                    $user = $userManager->findUserByUsername($lastUsername);
                    if ($user) {
                        $tracking->setUserId($user);
                    }
                }

                if (!$user) {
                    //Also track if no user is found. User 0 could be grouped by IP
                    $tracking->setUserId(0);
                }

                $tracking->setTrackDate(new DateTime());
                $tracking->setTrackIp($clientIp);
                $tracking->setTrackReferrer($referrer);
                $tracking->setTrackTarget($targetUrl);

                $tracking->setAdd1('failure');

                $rest = array();
                if ($event->getAuthenticationException()) {
                    $rest['error'] = $event->getAuthenticationException()->getMessage();
                } else {
                    $rest['error'] = '';
                }
                $tracking->setRest($rest);
                //$tracking->setRest($ignoredTrackingDomains);

                $tracking->setType('login');

                $this->trackingManager->saveTracking($tracking);
            }
        }
    }
}
