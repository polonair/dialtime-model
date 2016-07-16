<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ParameterRepository extends EntityRepository
{
    public function loadArray($name)
    {
    	if ($name === null) return [];
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT p FROM ModelBundle:Parameter p JOIN ModelBundle:ParameterVersion pv WITH p.actual = pv.id WHERE pv.name = :name');
        $query
        	->setParameter('name', $name)
        	->setMaxResults(1);
        $result = $query->getResult();
        if (count($result)>0) return json_decode($result[0]->getValue(), true);
        return [];
    }
    public function loadValue($name)
    {
        if ($name === null) return null;
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT p FROM ModelBundle:Parameter p JOIN ModelBundle:ParameterVersion pv WITH p.actual = pv.id WHERE pv.name = :name');
        $query
            ->setParameter('name', $name)
            ->setMaxResults(1);
        $result = $query->getResult();
        if (count($result)>0) return $result[0]->getValue();
        return null;
    }
}