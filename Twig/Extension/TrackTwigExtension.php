<?php

namespace WEBMI\TrackingBundle\Twig\Extension;

use WEBMI\TrackingBundle\Entity\Tracking;

class TrackTwigExtension extends \Twig_Extension {

    private $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            'track'  => new \Twig_Function_Method($this, 'getTrackingUrl')
        );
    }

    public function getTrackingUrl($linkRoute, $linkOptions = array(), $type = 'link') 
    {
        $linkUrl = $this->container->get('router')->generate($linkRoute, $linkOptions);
        $linkUrl = base64_encode($linkUrl);
        //$linkUrl = urlencode($linkUrl);
        $trackingUrl = $this->container->get('router')->generate('_track_action', array('url' => $linkUrl, 'type' => $type));
        return $trackingUrl;
    }

    public function getName()
    {
        return 'track';
    }

}