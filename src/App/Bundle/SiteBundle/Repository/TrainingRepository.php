<?php

namespace App\Bundle\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Created by PhpStorm.
 * User: Luk
 * Date: 02/02/2017
 * Time: 16:51
 */
class TrainingRepository  extends EntityRepository
{

    public function getExistingTraining($userId, $day, $startTime, $endTime)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t1 FROM AppSiteBundle:Training t1 WHERE t1.userId =:user AND t1.day = :day AND t1.id IN (SELECT t2.id FROM AppSiteBundle:Training t2 WHERE (t2.startTime > :startTime AND t2.endTime < :endTime) OR (t2.startTime < :startTime AND t2.endTime > :endTime) OR (t2.endTime > :startTime AND t2.startTime < :startTime) OR (t2.startTime < :endTime AND t2.endTime > :endTime))'
            )
            ->setParameter('user', $userId)
            ->setParameter('day', $day)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getResult();
    }


}