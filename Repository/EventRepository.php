<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Event;

class EventRepository extends EntityRepository
{
    public function loadUnprocessed()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT event
            FROM ModelBundle:Event event
            WHERE event.processed IS NULL");
        $result = $query->getResult();
        return $result;
    }
}
