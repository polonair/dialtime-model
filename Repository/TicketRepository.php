<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\Ticket;
use Polonairs\Dialtime\ModelBundle\Entity\Manager;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;

class TicketRepository extends EntityRepository
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
    public function loadAllIdsForPartner(Partner $partner, $time)
    {
        $data = $this
            ->getEntityManager()
            ->createQuery("
                SELECT ticket
                FROM ModelBundle:Ticket ticket
                JOIN ModelBundle:TicketVersion ticketVersion WITH ticket.actual = ticketVersion.id
                LEFT JOIN ModelBundle:TicketMessage message WITH ticket.id = message.ticket
                WHERE ticketVersion.client = :user
                    AND (ticketVersion.created_at > :time
                        OR ticket.removed_at > :time
                        OR message.created_at > :time)
                GROUP BY ticket")
            ->setParameter("user", $partner->getUser())
            ->setParameter("time", (new \DateTime())->setTimestamp($time))
            ->getResult();
        $result = [];
        foreach ($data as $obj) if ($obj instanceof Ticket) $result[] = $obj->getId();
        return $result;
    }
	public function loadOneForManager($manager, $id)
	{
        $em = $this->getEntityManager();
        $query = $this->getEntityManager()->createQuery("
        	SELECT ticket, ticketVersion, user, userVersion, master, masterVersion, partner, partnerVersion
        	FROM ModelBundle:Ticket ticket
        	JOIN ModelBundle:TicketVersion ticketVersion WITH ticket.actual = ticketVersion.id
                JOIN ModelBundle:User user WITH user.id = ticketVersion.client
                JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
                LEFT JOIN ModelBundle:Master master WITH user.id = master.user
                LEFT JOIN ModelBundle:MasterVersion masterVersion WITH master.actual = masterVersion.id
                LEFT JOIN ModelBundle:Partner partner WITH user.id = partner.user
                LEFT JOIN ModelBundle:PartnerVersion partnerVersion WITH partner.actual = partnerVersion.id
        	WHERE (masterVersion.manager = :manager OR  partnerVersion.manager = :manager) AND ticket.id = :id")
        ->setParameter('manager', $manager)
        ->setParameter('id', $id);
        $data = $query->getResult(); 
        foreach($data as $object) if ($object instanceof Ticket) return $object;
        return null;
	}
	public function loadAllIdsForManager(Manager $manager, $time)
	{
        $em = $this->getEntityManager();
        $query = $this->getEntityManager()->createQuery("
        	SELECT ticket, ticketVersion, ticketMessage
        	FROM ModelBundle:Ticket ticket
        	JOIN ModelBundle:TicketVersion ticketVersion WITH ticket.actual = ticketVersion.id
            JOIN ModelBundle:User user WITH user.id = ticketVersion.client
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
            LEFT JOIN ModelBundle:Master master WITH user.id = master.user
            LEFT JOIN ModelBundle:MasterVersion masterVersion WITH master.actual = masterVersion.id
            LEFT JOIN ModelBundle:Partner partner WITH user.id = partner.user
            LEFT JOIN ModelBundle:PartnerVersion partnerVersion WITH partner.actual = partnerVersion.id
        	LEFT JOIN ModelBundle:TicketMessage ticketMessage WITH ticketMessage.ticket = ticket.id
        	WHERE 
                (masterVersion.manager = :manager OR  partnerVersion.manager = :manager) AND 
                (ticketVersion.created_at > :from OR ticketMessage.created_at > :from)
        	GROUP BY ticket")
        ->setParameter('manager', $manager)
        ->setParameter('from', (new \DateTime())->setTimestamp($time));
        $data = $query->getResult();
        $result = [];
        foreach($data as $object) if ($object instanceof Ticket) $result[] = $object->getId();
        return $result;
	}
	public function loadTicketsByUser(User $user)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT ticket, ticketVersion
			FROM ModelBundle:Ticket ticket
			JOIN ModelBundle:TicketVersion ticketVersion WITH ticket.actual = ticketVersion.id
			WHERE ticketVersion.client = :user
			ORDER BY ticketVersion.created_at ASC");
		$query->setParameter("user", $user->getId());
		$result = $query->getresult();
		$tickets = [];
		foreach($result as $r) if ($r instanceof Ticket) $tickets[] = $r;
		return $tickets;
	}
}