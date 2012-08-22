<?php

namespace WEBMI\TrackingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use WEBMI\TrackingBundle\Entity\Tracking;
use DateTime;

class TrackController extends Controller
{
    /**
     * @Route("/track/{type}/{url}", name="_track_action")
     * @Template()
     */
    public function trackAction($type, $url)
    {
        //return new Response('ALLES GUT');
        
        $request = $this->getRequest();
        
        $user = $this->get('security.context')->getToken()->getUser();
        
        $referrer = $request->headers->get('referer');
        $clientIp = $request->getClientIp();
        
        $targetUrl = base64_decode($url);
        
        $trackingManager = $this->get('webmi_tracking.tracking_manager');
        
        $tracking = new Tracking();
        $tracking->setUserId($user);
        $tracking->setTrackDate(new DateTime());
        $tracking->setTrackIp($clientIp);
        $tracking->setTrackReferrer($referrer);
        $tracking->setTrackTarget($targetUrl);
        $tracking->setType($type);
        
        $trackingManager->saveTracking($tracking);
        
        return $this->redirect($targetUrl);
    }
}
