<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\Ticket;

class TicketRepository extends EntityRepository
{
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