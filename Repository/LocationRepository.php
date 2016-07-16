<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class LocationRepository extends EntityRepository
{
    public function loadIndexed()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c, cv FROM ModelBundle:Location c JOIN ModelBundle:LocationVersion cv WITH c.actual = cv.id');
        $locations = $query->getResult();
        $result = null;
        for ($i = 0; $i < count($locations); $i+=2) $result[$locations[$i]->getId()] = $locations[$i];
        //dump($locations);
        //dump($result);
        return $result;
    }
}