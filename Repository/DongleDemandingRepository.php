<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Ticket;
use Polonairs\Dialtime\ModelBundle\Entity\Campaign;
use Polonairs\Dialtime\ModelBundle\Entity\DongleDemanding;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DongleDemandingRepository extends EntityRepository
{
	public function loadAllForCampaign(Campaign $campaign)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT demanding, ticket, ticketVersion
			FROM ModelBundle:DongleDemanding demanding
			JOIN ModelBundle:Ticket ticket WITH demanding.ticket = ticket.id
			JOIN ModelBundle:TicketVersion ticketVersion WITH ticket.actual = ticketVersion.id
			WHERE demanding.campaign = :campaign");
		$query->setParameter("campaign", $campaign->getId());
		$result = $query->getResult();
		$demandings = [];
		foreach($result as $r) if ($r instanceof DongleDemanding) $demandings[] = $r;
		return $demandings;
	}
    public function loadForTicket(Ticket $ticket)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT dd FROM ModelBundle:DongleDemanding dd WHERE dd.ticket = :ticket');
        $query->setParameter('ticket', $ticket->getId());
        $demandings = $query->getResult();
        if (count($demandings) > 0) return $demandings[0];
        return null;
    }
}