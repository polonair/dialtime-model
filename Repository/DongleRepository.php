<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Dongle;
use Polonairs\Dialtime\ModelBundle\Entity\DongleDemanding;
use Polonairs\Dialtime\ModelBundle\Entity\Phone;
use Polonairs\Dialtime\ModelBundle\Entity\Gate;
use Polonairs\Dialtime\ModelBundle\Entity\Campaign;

class DongleRepository extends EntityRepository
{
	public function loadAllSuggestedIds(DongleDemanding $entry)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE dongleVersion.campaign IS NULL");
		$data = $query->getResult();
		$result = [];
		foreach($data as $dongle) if ($dongle instanceof Dongle) $result[] = $dongle->getId();
		return $result;
	}
	public function loadOneForManager($manager, $id)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle 
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id		
			WHERE dongle.id = :id")
		->setParameter("id", $id);
		$result = $query->getResult();
		foreach($result as $r) if ($r instanceof Dongle) return $r;
		return null;
	}
	public function loadOneForPartner($partner, $id)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle 
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			JOIN ModelBundle:Campaign campaign WITH dongleVersion.campaign = campaign.id
			JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id			
			WHERE dongle.id = :id AND campaignVersion.owner = :owner")
		->setParameter("id", $id)
		->setParameter("owner", $partner);
		$result = $query->getResult();
		foreach($result as $r) if ($r instanceof Dongle) return $r;
		return null;
	}
	public function loadAllIdsForCampaign($campaign)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle 
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE dongleVersion.campaign = :campaign");
		$query->setParameter("campaign", $campaign);
		$result = $query->getResult();
		$dongles = [];
		foreach($result as $r) if ($r instanceof Dongle) $dongles[] = $r->getId();
		return $dongles;
	}
	public function loadAllForGate(Gate $gate)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle 
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE dongleVersion.gate = :gate");
		$query->setParameter("gate", $gate->getId());
		$result = $query->getResult();
		$dongles = [];
		foreach($result as $r) if ($r instanceof Dongle) $dongles[$r->getId()] = $r;
		return $dongles;		
	}
	public function loadAllForCampaign(Campaign $campaign)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle 
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE dongleVersion.campaign = :campaign");
		$query->setParameter("campaign", $campaign->getId());
		$result = $query->getResult();
		$dongles = [];
		foreach($result as $r) if ($r instanceof Dongle) $dongles[] = $r;
		return $dongles;
	}
	public function suggestTerminator(Phone $phone, Dongle $exclude)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle.id
			FROM ModelBundle:Route route
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			JOIN ModelBundle:Dongle dongle WITH routeVersion.terminator = dongle.id
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE routeVersion.master_phone = :phone");
		$query->setParameter("phone", $phone->getId());
		$data = $query->getResult();
		$ids = [];
		foreach($data as $item) $ids[] = $item['id'];
		$ids[] = $exclude->getId();

		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE dongle.id NOT IN (:ids)");
		$query->setParameter("ids", $ids);
		$data = $query->getResult();
		$result = [];
		foreach($data as $dongle) if ($dongle instanceof Dongle) return $dongle;
		return null;
	}
	public function loadAllFree()
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE dongleVersion.campaign IS NULL");
		$data = $query->getResult();
		$result = [];
		foreach($data as $dongle) if ($dongle instanceof Dongle) {$result[] = $dongle;}
		return $result;
	}
	public function loadAll()
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion, campaign, gate
			FROM ModelBundle:Dongle dongle
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			LEFT JOIN ModelBundle:Campaign campaign WITH dongleVersion.campaign = campaign.id
			LEFT JOIN ModelBundle:Gate gate WITH dongleVersion.gate = gate.id");
		$data = $query->getResult();
		$result = [];
		foreach($data as $dongle) if ($dongle instanceof Dongle) $result[$dongle->getId()] = $dongle;
		return $result;
	}
	public function loadByNumber($number)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT dongle, dongleVersion
			FROM ModelBundle:Dongle dongle
			JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
			WHERE dongleVersion.number = :number");
		$query->setParameter("number", $number);
		$data = $query->getResult();
		$result = [];
		foreach($data as $dongle) if ($dongle instanceof Dongle) return $dongle;
		return null;
	}
}