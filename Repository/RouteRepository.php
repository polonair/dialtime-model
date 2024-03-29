<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Route;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Polonairs\Dialtime\ModelBundle\Entity\Call;
use Polonairs\Dialtime\ModelBundle\Entity\Dongle;
use Polonairs\Dialtime\ModelBundle\Entity\TransactionEntry;
use Polonairs\Dialtime\ModelBundle\Entity\RouteRejection;
use Polonairs\Dialtime\ModelBundle\Entity\Phone;
use Polonairs\Dialtime\ModelBundle\Entity\Offer;

class RouteRepository extends EntityRepository
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
                SELECT route
                FROM ModelBundle:Route route
                JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
				JOIN ModelBundle:Task task WITH routeVersion.task = task.id 
				JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id 
				JOIN ModelBundle:Campaign campaign WITH taskVersion.campaign = campaign.id 
				JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id
                WHERE campaignVersion.owner = :partner 
                	AND (route.created_at > :time
                		OR routeVersion.created_at > :time)
                GROUP BY route")
            ->setParameter("partner", $partner)
            ->setParameter("time", (new \DateTime())->setTimestamp($time))
            ->getResult();
        $result = [];
        foreach ($data as $obj) if ($obj instanceof Route) $result[] = $obj->getId();
        return $result;
    }
	public function loadOneForMaster(Master $master, $id)
	{
        $em = $this->getEntityManager();
        $query = $this->getEntityManager()->createQuery("
			SELECT
				route, routeVersion, 
				phone, phoneVersion, 
				terminator, terminatorVersion, 
				task, taskVersion, 
				offer, offerVersion
			FROM ModelBundle:Route route
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id 
			JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id 
			JOIN ModelBundle:Dongle terminator WITH routeVersion.terminator = terminator.id 
			JOIN ModelBundle:DongleVersion terminatorVersion WITH terminator.actual = terminatorVersion.id 
			JOIN ModelBundle:Task task WITH routeVersion.task = task.id 
			JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id 
			JOIN ModelBundle:Offer offer WITH taskVersion.offer = offer.id 
			JOIN ModelBundle:OfferVersion offerVersion WITH offer.actual = offerVersion.id 
			WHERE phoneVersion.owner = :master AND route.id = :id")
        	->setParameter('master', $master->getUser())
	        ->setParameter('id', $id);
        $data = $query->getResult();
        $route = null;
        foreach($data as $object) if ($object instanceof Route) { $route = $object; break; }
        if ($route !== null)
        {
        	$query = $this->getEntityManager()->createQuery("
				SELECT
					entry, transaction, call
				FROM ModelBundle:TransactionEntry entry
				JOIN ModelBundle:Transaction transaction WITH entry.transaction = transaction.id 
				JOIN ModelBundle:Call call WITH transaction.id = call.transaction
				WHERE call.direction IN (:dirs) AND entry.role = :role AND call.route = :route")
				->setParameter('dirs', [ Call::DIRECTION_RG, Call::DIRECTION_RRG ])
				->setParameter('role', 'BUYER')
				->setParameter('route', $route);
        	$data = $query->getResult();
        	foreach($data as $object) if ($object instanceof TransactionEntry) { $route->setAttachment(["cost" => $object->getAmount()]); break; }
        }
        return $route;
	}
	public function loadAllIdsForMaster(Master $master, $time)
	{
        $em = $this->getEntityManager();
        $query = $this->getEntityManager()->createQuery("
			SELECT call, route, routeVersion, phone, phoneVersion
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id 
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id 
			JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id 
			WHERE phoneVersion.owner = :master AND call.created_at > :from
			GROUP BY route
			ORDER BY call.created_at ASC");
        $query->setParameter('master', $master->getUser())->setParameter('from', (new \DateTime())->setTimestamp($time));
        $data = $query->getResult();
        $result = [];
        foreach($data as $object) if ($object instanceof Route) $result[] = $object->getId();
        return $result;
	}
	public function loadActiveForMasterPhone(Phone $phone)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT route, routeVersion
			FROM ModelBundle:Route route
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			WHERE routeVersion.master_phone = :phone");
		$query->setParameter("phone", $phone->getId());
		$result = $query->getResult();
		$routes = [];
		foreach($result as $r) if ($r instanceof Route) $routes[$r->getId()] = $r;
		return $routes;
	}
	public function loadRouteForPartnership($customer, Master $master)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT route, routeVersion
			FROM ModelBundle:Route route
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id
			JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id
			WHERE routeVersion.customer_number = :customer AND
				  phoneVersion.owner = :master");
		$query
			->setParameter("customer", $customer)
			->setParameter("master", $master->getUser()->getId());
		$result = $query->getResult();
		foreach($result as $r) if ($r instanceof Route) return $r;
		return null;		
	}
	public function loadRouteForOrigination($customer, Dongle $originator)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT route, routeVersion
			FROM ModelBundle:Route route
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			WHERE routeVersion.customer_number = :customer AND
				routeVersion.originator = :originator AND
				routeVersion.state = :state");
		$query
			->setParameter("customer", $customer)
			->setParameter("originator", $originator->getId())
			->setParameter("state", Route::STATE_ACTIVE);
		$result = $query->getResult();
		foreach($result as $r) if ($r instanceof Route) return $r;
		return null;
	}
	public function loadActive()
	{
		$query = $this->getEntityManager()->createQuery("
			SELECT route, routeVersion, phone
			FROM ModelBundle:Route route 
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id
			WHERE routeVersion.expired_at > :now");
		$query->setParameter('now', new \DateTime("now"));
		$routes = $query->getResult();
		$result = [];
		foreach ($routes as $route) if ($route instanceof Route) $result[$route->getId()] = $route;
		return $result;
	}
	public function loadUnbilledRoutes()
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT route, routeVersion
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			WHERE call.direction IN (:dirs) AND call.transaction IS NULL AND routeVersion.expired_at > :now
			GROUP BY route.id");
		$query->setParameter('dirs', [ Call::DIRECTION_RG, Call::DIRECTION_RRG ]);
		$query->setParameter('now', new \DateTime("now"));
		$result = $query->getResult();
		$routes = [];
		foreach($result as $r)if ($r instanceof Route) $routes[] = $r;
		return $routes;
	}
	public function loadByMaster(Master $master)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT call,
			       route, 
			       routeVersion, 
			       phone,
			       phoneVersion,
			       terminatorDongle,
			       terminatorDongleVersion
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id
			JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id
			JOIN ModelBundle:Dongle terminatorDongle WITH routeVersion.terminator = terminatorDongle.id
			JOIN ModelBundle:DongleVersion terminatorDongleVersion WITH terminatorDongle.actual = terminatorDongleVersion.id
			WHERE phoneVersion.owner = :owner");
		$query->setParameter("owner", $master->getUser()->getId());
		$data = $query->getResult();

		$result = [];
		foreach ($data as $d) 
		{
			if ($d instanceof Route) $result[$d->getId()] = $d;
		}
		$calls = [];
		foreach ($data as $d) 
		{
			if ($d instanceof Call) $calls[$d->getRoute()->getId()][] = $d;
		}
		foreach($result as $route)
		{
			$route->setAttachment(["calls" => $calls[$route->getId()]]);
		}

		return $result;
	}
	public function loadByPartner(Partner $partner)
	{
		$query = $this->getEntityManager()->createQuery("
			SELECT route, routeVersion, task, taskVersion, campaign, campaignVersion 
			FROM ModelBundle:Route route
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			JOIN ModelBundle:Task task WITH routeVersion.task = task.id
			JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id
			JOIN ModelBundle:Campaign campaign WITH taskVersion.campaign = campaign.id
			JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id
			WHERE campaignVersion.owner = :owner");
		$query->setParameter('owner', $partner->getId());
		$jobs = $query->getResult();
		$result = [];
		foreach ($jobs as $j) if ($j instanceof Route) $result[] = $j;
		return $result;
	}
	public function loadLatestForOffer(Offer $offer)
	{
		$query = $this->getEntityManager()->createQuery("
			SELECT 
				route,
				routeVersion,
				task,
				taskVersion,
				offer,
				offerVersion
			FROM ModelBundle:Route route
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			JOIN ModelBundle:Task task WITH routeVersion.task = task.id
			JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id
			JOIN ModelBundle:Offer offer WITH taskVersion.offer = offer.id
			JOIN ModelBundle:OfferVersion offerVersion WITH offer.actual = offerVersion.id
			WHERE offer.id = :offer
			ORDER BY route.created_at ASC");
		$query->setParameter('offer', $offer->getId());
		$data = $query->getResult();
		foreach ($data as $d) if ($d instanceof Route) return $d;
		return null;
	}
}