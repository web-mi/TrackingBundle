<?php

/*
 * This file is part of the WEBMITrackingBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WEBMI\TrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="webmi__tracking")
 */
class Tracking implements TrackingInterface
{   
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $userId;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $trackDate;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $trackIp;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $type;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $trackReferrer;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $trackTarget;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $add1;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $add2;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $add3;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $add4;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $add5;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $rest;
    
    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Returns the user id.
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * @return mixed
     */
    public function setUserId($user)
    {
        if(is_object($user)) {
            $this->userId = $user->getId();
        } else {
            $this->userId = $user;
        }
        
        return $this;
    }

    /**
     * @return string
     */
    public function getTrackIp()
    {
        return $this->trackIp;
    }
    
    /**
     * Sets the login IP.
     *
     * @param string $loginIp
     * @return string
     */
    public function setTrackIp($ip)
    {
        $this->trackIp = $ip;

        return $this;
    }
    
    /**
     * @return string
     */
    public function getTrackReferrer()
    {
        return $this->trackReferrer;
    }
    
    /**
     * Sets the login Referrer.
     *
     * @param string $loginReferrer
     * @return string
     */
    public function setTrackReferrer($referrer)
    {
        $this->trackReferrer = $referrer;

        return $this;
    }
    
    /**
     * Gets the login date.
     *
     * @return \DateTime
     */
    public function getTrackDate()
    {
        return $this->trackDate;
    }
    
    /**
     * Sets the login date
     *
     * @param \DateTime $time
     * @return Date
     */
    public function setTrackDate(\DateTime $time)
    {
        $this->trackDate = $time;

        return $this;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Tracking
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set trackTarget
     *
     * @param string $trackTarget
     * @return Tracking
     */
    public function setTrackTarget($trackTarget)
    {
        $this->trackTarget = $trackTarget;
        return $this;
    }

    /**
     * Get trackTarget
     *
     * @return string 
     */
    public function getTrackTarget()
    {
        return $this->trackTarget;
    }
    
    /**
     * Set one Add
     *
     * @param int $addNumber
     * @param string $add
     * @return Tracking
     */
    public function setAdd($addNumber, $add)
    {
        $method = 'setAdd'.$addNumber;
        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $addNumber, get_class($this)));
        }
        $this->$method($add);
        return $this;
    }

    /**
     * Get one Add By Number
     *
     * @return mixed
     */
    public function getAdd($addNumber)
    {
        $method = 'getAdd'.$addNumber;
        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $addNumber, get_class($this)));
        }
        return $this->$method();
    }
    
    /**
     * Set Rest
     *
     * @param string $add
     * @return Tracking
     */
    public function setRest($rest = array())
    {
        $this->rest = serialize($rest);
        return $this;
    }

    /**
     * Get Rest
     *
     * @return array
     */
    public function getRest()
    {
        $rest = unserialize($this->rest);
        return $rest;
    }
    

    /**
     * Set add1
     *
     * @param string $add1
     * @return Tracking
     */
    public function setAdd1($add1)
    {
        $this->add1 = $add1;
        return $this;
    }

    /**
     * Get add1
     *
     * @return string 
     */
    public function getAdd1()
    {
        return $this->add1;
    }

    /**
     * Set add2
     *
     * @param string $add2
     * @return Tracking
     */
    public function setAdd2($add2)
    {
        $this->add2 = $add2;
        return $this;
    }

    /**
     * Get add2
     *
     * @return string 
     */
    public function getAdd2()
    {
        return $this->add2;
    }

    /**
     * Set add3
     *
     * @param string $add3
     * @return Tracking
     */
    public function setAdd3($add3)
    {
        $this->add3 = $add3;
        return $this;
    }

    /**
     * Get add3
     *
     * @return string 
     */
    public function getAdd3()
    {
        return $this->add3;
    }

    /**
     * Set add4
     *
     * @param string $add4
     * @return Tracking
     */
    public function setAdd4($add4)
    {
        $this->add4 = $add4;
        return $this;
    }

    /**
     * Get add4
     *
     * @return string 
     */
    public function getAdd4()
    {
        return $this->add4;
    }

    /**
     * Set add5
     *
     * @param string $add5
     * @return Tracking
     */
    public function setAdd5($add5)
    {
        $this->add5 = $add5;
        return $this;
    }

    /**
     * Get add5
     *
     * @return string 
     */
    public function getAdd5()
    {
        return $this->add5;
    }
}