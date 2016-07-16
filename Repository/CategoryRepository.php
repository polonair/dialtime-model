<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CategoryRepository extends EntityRepository
{
    public function loadIndexed()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c, cv FROM ModelBundle:Category c JOIN ModelBundle:CategoryVersion cv WITH c.actual = cv.id');
        $categories = $query->getResult();
        $result = null;
        for ($i = 0; $i < count($categories); $i+=2) $result[$categories[$i]->getId()] = $categories[$i];
        //dump($categories);
        //dump($result);
        return $result;
    }
}