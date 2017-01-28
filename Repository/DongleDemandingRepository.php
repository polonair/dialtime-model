<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Ticket;
use Polonairs\Dialtime\ModelBundle\Entity\Campaign;
use Polonairs\Dialtime\ModelBundle\Entity\DongleDemanding;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DongleDemandingRepository extends EntityRepository
{
	public function loadOneForTicket(Ticket $entry)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongleDemanding
			FROM ModelBundle:DongleDemanding dongleDemanding
			WHERE dongleDemanding.ticket = :ticket")
		->setParameter("ticket", $entry);
		$result = $query->getResult();
		foreach($result as $r) if ($r instanceof DongleDemanding) return $r;
		return null;
	}
	public function loadOneForManager($manager, $id)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongleDemanding, dongle, dongleVersion
			FROM ModelBundle:DongleDemanding dongleDemanding  
			LEFT JOIN ModelBundle:Dongle dongle WITH dongleDemanding.dongle = dongle.id
			LEFT JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE dongleDemanding.id = :id")
		->setParameter("id", $id);
		$result = $query->getResult();
		foreach($result as $r) if ($r instanceof DongleDemanding) return $r;
		return null;
	}
	public function loadOneForPartner($partner, $id)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongleDemanding
			FROM ModelBundle:DongleDemanding dongleDemanding 
			JOIN ModelBundle:Campaign campaign WITH dongleDemanding.campaign = campaign.id
			JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id			
			WHERE dongleDemanding.id = :id AND campaignVersion.owner = :owner")
		->setParameter("id", $id)
		->setParameter("owner", $partner);
		$result = $query->getResult();
		foreach($result as $r) if ($r instanceof DongleDemanding) return $r;
		return null;
	}
	public function loadAllIdsForCampaign($campaign)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongleDemanding
			FROM ModelBundle:DongleDemanding dongleDemanding 
			WHERE dongleDemanding.campaign = :campaign AND dongleDemanding.state = :state")
		->setParameter("campaign", $campaign)
		->setParameter("state", 'WAIT');
		$result = $query->getResult();
		$dongles = [];
		foreach($result as $r) if ($r instanceof DongleDemanding) $dongles[] = $r->getId();
		return $dongles;
	}
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