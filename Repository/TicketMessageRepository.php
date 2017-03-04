<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\Ticket;
use Polonairs\Dialtime\ModelBundle\Entity\Manager;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;

class TicketMessageRepository extends EntityRepository
{
    /*public function _loadAllIdsForPartner(Partner $partner, $time)
    {
        $data = $this
            ->getEntityManager()
            ->createQuery("
                SELECT
                FROM
                JOIN
                LEFT JOIN
                WHERE
                AND
                GROUP BY
                ORDER BY")
            ->setParameter("partner", $partner)
            ->setParameter("time", (new \DateTime())->setTimestamp($time))
            ->getResult();
        return null;
    }*/
}