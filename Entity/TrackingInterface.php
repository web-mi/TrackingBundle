<?php

/*
 * This file is part of the WEBMITrackingBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WEBMI\TrackingBundle\Entity;

interface TrackingInterface
{   
    /**
     * Returns the user id.
     *
     * @return mixed
     */
    public function getUserId();
    
    /**
     * @return mixed
     */
    public function setUserId($user);

    /**
     * @return string
     */
    public function getTrackIp();
    
    /**
     * Sets the login IP.
     *
     * @param string $loginIp
     * @return string
     */
    public function setTrackIp($ip);
    
    /**
     * @return string
     */
    public function getTrackReferrer();
    
    /**
     * Sets the login Referrer.
     *
     * @param string $loginReferrer
     * @return string
     */
    public function setTrackReferrer($referrer);
    
    /**
     * Gets the login date.
     *
     * @return \DateTime
     */
    public function getTrackDate();
    
    /**
     * Sets the login date
     *
     * @param \DateTime $time
     * @return Date
     */
    public function setTrackDate(\DateTime $time);
}
