<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\LetterSending;

class LetterSendingRepository extends EntityRepository
{
    public function loadUnprocessed()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT letterSending
            FROM ModelBundle:LetterSending letterSending
            WHERE letterSending.sent_on IS NULL");
        $result = $query->getResult();
        return $result;
    }
}
