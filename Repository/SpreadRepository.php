<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Spread;

class SpreadRepository extends EntityRepository
{
    public function loadMatrix()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT s, sv, l, c FROM ModelBundle:Spread s JOIN ModelBundle:SpreadVersion sv WITH s.actual = sv.id JOIN ModelBundle:Category c WITH c.id = sv.category JOIN ModelBundle:Location l WITH l.id = sv.location');
        $spreads = $query->getResult();
        $result = [];
        for ($i = 0; $i < count($spreads); $i++) 
        {
            //dump($spreads[$i]);
            if ($spreads[$i] instanceof Spread)
            {
                $result[$spreads[$i]->getCategory()->getId()][$spreads[$i]->getLocation()->getId()] = $spreads[$i];
            }
        }
        //dump($campaigns);
        //dump($result);
        return $result;
    }
}