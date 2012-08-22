<?php

/*
 * This file is part of the WEBMITrackingBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WEBMI\TrackingBundle\Entity;

use Symfony\Component\Validator\Constraint;

interface TrackingManagerInterface
{
    /**
     * Sets new Tracking.
     *
     * @param TrackingInterface $tracking
     */
    function saveTracking(TrackingInterface $tracking);
}
