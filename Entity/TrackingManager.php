<?php

/*
 * This file is part of the WEBMITrackingBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Description of TrackingManager
 *
 * @author Marc Itzenthaler
 */

namespace WEBMI\TrackingBundle\Entity;

use Doctrine\ORM\EntityManager;
use WEBMI\TrackingBundle\Entity\TrackingInterface;
use WEBMI\TrackingBundle\Entity\TrackingManagerInterface;

class TrackingManager implements TrackingManagerInterface 
{
    
    protected $em;
    protected $class;
    protected $repository;
    
    /**
     * Constructor.
     *
     * @param EntityManager           $em
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }
    
    /**
     * Sets new Tracking.
     *
     * @param TrackingInterface $tracking
     */
    public function saveTracking(TrackingInterface $tracking)
    {
        $this->em->persist($tracking);
        $this->em->flush();
    }
    
    /**
     * Gets Tracking By Criteria
     * 
     * @param array $criteria 
     */
    public function getTrackingBy(array $criteria)
    {
        $order = array(
            'userId' => 'ASC',
            'trackDate' => 'DESC'
            );
        return $this->repository->findOneBy($criteria, $order);
    }
    
    /**
     * Gets Trackings By Criteria
     * 
     * @param array $criteria 
     * @param array $groupings
     * @return array [0] => counter, [1] => trackings Object
     */
    public function getTrackingsBy(array $criteria, array $groupings = array())
    {
        $order = array(
            'userId' => 'ASC',
            'trackDate' => 'DESC'
            );
        
        $whereCounter = 1;
        
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(a.id) as counter, a');
        $qb->from($this->class, 'a');
        foreach ($criteria as $crow => $cvalue) {
            if (is_array($cvalue)) {
                foreach ($cvalue as $csrow => $csvalue) {
                    if ($csvalue instanceof \DateTime) {
                        $qb->andWhere(
                                $qb->expr()->$crow("STRTODATEFORMAT(a.".$csrow.", '%Y-%m-%d %H:%i:%s', '%Y-%m-%d')", '?'.$whereCounter)
                        );
                        $qb->setParameter($whereCounter, $csvalue->format('Y-m-d'));
                    } else {
                        $qb->andWhere(
                                $qb->expr()->$crow('a.'.$csrow, '?'.$whereCounter)
                        );
                        $qb->setParameter($whereCounter, $csvalue);
                    }
                    $whereCounter++;
                }
            } else {
                $qb->andWhere(
                    $qb->expr()->andX(
                        $qb->expr()->eq('a.'.$crow, '?'.$whereCounter)
                    )
                );
                $qb->setParameter($whereCounter, $cvalue);
                $whereCounter++;
            }
        }
        
        foreach ($order as $orow => $osorting) {
            $qb->orderBy('a.'.$orow, $osorting);
        }
        
        foreach ($groupings as $grouping) {
            $qb->addGroupBy('a.'.$grouping);
        }
        
        return $qb->getQuery()->execute();
        //return $this->repository->findBy($criteria, $order);
    }
    
    /**
     * Gets TrackingCounter for all types from a user
     * 
     * @param array $trackingTypes
     * 
     * @return array loaded[$userId][$loadType] 
     */
    public function getTrackingArticlesGroupedByUser(array $trackingTypes)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('count(t.id) as counter, t.userId, t.type')
                ->from($this->class, 't')
                ->add('where', 
                        $qb->expr()->andx(
                                    $qb->expr()->in('t.type', '?1')
                                )
                        )
                ->addOrderBy('t.userId')
                ->addOrderBy('t.type')
                ->addGroupBy('t.userId')
                ->addGroupBy('t.type')
                ->setParameter(1, $trackingTypes);
        $userArticles = $qb->getQuery()->getResult();
        
        $tracked = array();
        foreach($userArticles as $userArticle) {
            $tracked[$userArticle['userId']][$userArticle['type']] = $userArticle['counter'];
        }
        
        return $tracked;
    }
    
    /**
     * Gets TrackingCounter for article types from a user
     * 
     * @param array $trackingTypes
     * 
     * @return array loaded[$userId][$loadType] 
     */
    public function getTrackingArticlesByUser($userId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('t.userId, t.type, t.trackDate, t.add1, count(t.add1) as viewed')
                ->from($this->class, 't')
                ->add('where', 
                        $qb->expr()->andx(
                                    $qb->expr()->eq('t.type', '?1'),
                                    $qb->expr()->eq('t.userId', '?2')
                                )
                        )
                ->addOrderBy('t.trackDate')
                ->addGroupBy('t.add1')
                ->setParameter(1, 'article')
                ->setParameter(2, $userId);
        $userArticles = $qb->getQuery()->getResult();
        
        $tracked = array();
        foreach($userArticles as $userArticle) {
            $tracked[$userArticle['add1']]['loaded'] = 0;
            $tracked[$userArticle['add1']]['date'] = $userArticle['trackDate'];
            $tracked[$userArticle['add1']]['user_id'] = $userArticle['userId'];
            $tracked[$userArticle['add1']]['loadCounter'] = 0;
            $tracked[$userArticle['add1']]['watchCounter'] = $userArticle['viewed'];
        }
        
        return $tracked;
    }
    
    /**
     * Gets TrackingCounter for image types from a user
     * 
     * @param array $trackingTypes
     * 
     * @return array loaded[$userId][$loadType] 
     */
    public function getTrackingImagesByUser($userId)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('t.userId, t.type, t.trackDate, t.add2, count(t.add2) as viewed, t.add1, t.add3')
                ->from($this->class, 't')
                ->add('where', 
                        $qb->expr()->andx(
                                    $qb->expr()->eq('t.type', '?1'),
                                    $qb->expr()->eq('t.userId', '?2')
                                )
                        )
                ->addOrderBy('t.trackDate')
                ->addGroupBy('t.add2')
                ->addGroupBy('t.add1')
                ->setParameter(1, 'image')
                ->setParameter(2, $userId);
        $userArticles = $qb->getQuery()->getResult();
        
        $tracked = array();
        foreach($userArticles as $userArticle) {
            $tracked[$userArticle['add2']][$userArticle['add3']]['loaded'] = 0;
            $tracked[$userArticle['add2']]['article'] = $userArticle['add1'];
            $tracked[$userArticle['add2']]['date'] = $userArticle['trackDate'];
            $tracked[$userArticle['add2']]['user_id'] = $userArticle['userId'];
            $tracked[$userArticle['add2']][$userArticle['add3']]['loadCounter'] = 0;
            $tracked[$userArticle['add2']][$userArticle['add3']]['watchCounter'] = $userArticle['viewed'];
        }
        
        return $tracked;
    }
    
    /**
     * Gets Trackings
     * 
     */
    public function findTrackings()
    {
        return $this->repository->findAll();
    }
}

?>
