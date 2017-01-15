<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Polonairs\Dialtime\ModelBundle\Entity\Location;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class LocationRepository extends EntityRepository
{
    public function loadOne($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT location, locationVersion 
            FROM ModelBundle:Location location 
            JOIN ModelBundle:LocationVersion locationVersion WITH location.actual = locationVersion.id
            WHERE location.id = :id');
        $query->setParameter('id', $id);
        $data = $query->getResult();
        foreach($data as $d) if ($d instanceof Location) return $d;
        return null;
    }
    public function loadIndexed()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT location, locationVersion
            FROM ModelBundle:Location location 
            JOIN ModelBundle:LocationVersion locationVersion WITH location.actual = locationVersion.id');
        $data = $query->getResult();
        $result = [];
        foreach($data as $d)
        {
            if ($d instanceof Location) $result[$d->getId()] = $d;
        }
        return $result;
    }
    public function isChildOrSame(Location $parent, Location $child)
    {
        //dump($parent->getId());
        //dump($child->getId());
        if ($parent->getId() === $child->getId()) return true;
        $result = $this->loadIndexed();
        //dump($result);
        if ($result[$child->getId()]->getParent() === null) return false;
        $location = $result[$child->getId()]->getParent()->getId();
        while (true)
        {
            //dump($location);
            if ($location === $parent->getId()) return true;
            if ($result[$location]->getParent() === null) return false;
            $location = $result[$location]->getParent()->getId();
        }
        return false;
    }
}