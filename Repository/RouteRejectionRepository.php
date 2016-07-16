<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Polonairs\Dialtime\ModelBundle\Entity\Route;
use Polonairs\Dialtime\ModelBundle\Entity\RouteRejection;

class RouteRejectionRepository extends EntityRepository
{
	public function loadAllForRoute(Route $route)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT routeRejection, routeRejectionVersion, route, routeVersion, task, taskVersion, campaign, campaignVersion
			FROM ModelBundle:RouteRejection routeRejection
			JOIN ModelBundle:RouteRejectionVersion routeRejectionVersion WITH routeRejection.actual = routeRejectionVersion.id
			JOIN ModelBundle:Route route WITH routeRejectionVersion.route = route.id
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			JOIN ModelBundle:Task task WITH routeVersion.task = task.id
			JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id
			JOIN ModelBundle:Campaign campaign WITH taskVersion.campaign = campaign.id
			JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id
			WHERE route.id = :route");
		$query->setParameter('route', $route->getId());
		$result = $query->getResult();
		$rejections = [];
		foreach($result as $r)if ($r instanceof RouteRejection) $rejections[] = $r;
		return $rejections;
	}
	public function loadAllByPartner(Partner $partner)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT routeRejection, routeRejectionVersion, route, routeVersion, task, taskVersion, campaign, campaignVersion
			FROM ModelBundle:RouteRejection routeRejection
			JOIN ModelBundle:RouteRejectionVersion routeRejectionVersion WITH routeRejection.actual = routeRejectionVersion.id
			JOIN ModelBundle:Route route WITH routeRejectionVersion.route = route.id
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			JOIN ModelBundle:Task task WITH routeVersion.task = task.id
			JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id
			JOIN ModelBundle:Campaign campaign WITH taskVersion.campaign = campaign.id
			JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id
			WHERE campaignVersion.owner = :partner");
		$query->setParameter('partner', $partner->getId());
		$result = $query->getResult();
		$rejections = [];
		foreach($result as $r)if ($r instanceof RouteRejection) $rejections[] = $r;
		return $rejections;
	}
	public function doPartnerApprove($id)
	{
		$em = $this->getEntityManager();
		$rejection = $em->getRepository("ModelBundle:RouteRejection")->findOneById($id);
		if ($rejection->getState() === RouteRejection::STATE_REJECTED_BY_MASTER)
		{
			$rejection->setState(RouteRejection::STATE_APPROVED_BY_PARTNER);
			$em->flush();
		}
	}
	public function doPartnerDecline($id)
	{
		$em = $this->getEntityManager();
		$rejection = $em->getRepository("ModelBundle:RouteRejection")->findOneById($id);
		if ($rejection->getState() === RouteRejection::STATE_REJECTED_BY_MASTER)
		{
			$rejection->setState(RouteRejection::STATE_DECLINED_BY_PARTNER);
			$em->flush();
		}
	}
}