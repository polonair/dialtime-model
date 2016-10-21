<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Spread;
use Polonairs\Dialtime\ModelBundle\Entity\SpreadVersion;
use Polonairs\Dialtime\ModelBundle\Entity\Campaign;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;

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
    public function loadByCampaign(Campaign $campaign)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT spread,
                spreadVersion
            FROM ModelBundle:Spread spread 
            JOIN ModelBundle:SpreadVersion spreadVersion WITH spread.actual = spreadVersion.id
            WHERE spreadVersion.category = :category AND spreadVersion.location = :location');
        $query->setParameter('category', $campaign->getCategory()->getId());
        $query->setParameter('location', $campaign->getLocation()->getId());
        $spreads = $query->getResult();
        if (count($spreads)>0) return $spreads[0];
        return null;
    }
    public function loadAll()
    {
        $data = $this
            ->getEntityManager()
            ->createQuery('
                SELECT spread,
                    spreadVersion,
                    category,
                    categoryVersion,
                    location,
                    locationVersion
                FROM ModelBundle:Spread spread 
                JOIN ModelBundle:SpreadVersion spreadVersion WITH spread.actual = spreadVersion.id
                JOIN ModelBundle:Category category WITH spreadVersion.category = category.id
                JOIN ModelBundle:CategoryVersion categoryVersion WITH category.actual = categoryVersion.id
                JOIN ModelBundle:Location location WITH spreadVersion.location = location.id
                JOIN ModelBundle:LocationVersion locationVersion WITH location.actual = locationVersion.id')
            ->getResult();
        $result = [];
        foreach($data as $spread) if ($spread instanceof Spread) $result[] = $spread;
        return $result;
    }
    public function loadOneById($id)
    {
        $data = $this
            ->getEntityManager()
            ->createQuery('
                SELECT spreadVersion,
                    spread,
                    category,
                    categoryVersion,
                    location,
                    locationVersion
                FROM ModelBundle:SpreadVersion spreadVersion 
                JOIN ModelBundle:Spread spread WITH spreadVersion.entity = spread.id
                JOIN ModelBundle:Category category WITH spreadVersion.category = category.id
                JOIN ModelBundle:CategoryVersion categoryVersion WITH category.actual = categoryVersion.id
                JOIN ModelBundle:Location location WITH spreadVersion.location = location.id
                JOIN ModelBundle:LocationVersion locationVersion WITH location.actual = locationVersion.id
                WHERE spread.id = :id')
            ->setParameter("id", $id)
            ->getResult();
        $result = [];
        $spread = null;
        foreach($data as $v) 
        {
            if ($v instanceof Spread) $spread = $v;
            if ($v instanceof SpreadVersion) $result[] = $v;
        }
        if ($spread !== null)
        {
            $spread->setHistory($result);
        }
        return $spread;
    }
}