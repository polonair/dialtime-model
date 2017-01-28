<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Call;
use Polonairs\Dialtime\ModelBundle\Entity\Route;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Polonairs\Dialtime\ModelBundle\Entity\Dongle;

class CallRepository extends EntityRepository
{
	public function loadOneForPartner(Partner $partner, $id)
	{	
        $em = $this->getEntityManager();

        $query = $em->createQuery("
        	SELECT call, route, routeVersion, task, taskVersion, campaign, campaignVersion
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id 
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			JOIN ModelBundle:Task task WITH routeVersion.task = task.id
			JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id 
			JOIN ModelBundle:Campaign campaign WITH taskVersion.campaign = campaign.id
			JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id 
			WHERE campaignVersion.owner = :partner AND call.id = :id 
			ORDER BY call.created_at ASC")
        ->setParameter('partner', $partner)
        ->setParameter('id', $id);
        $jobs = $query->getResult();
        foreach ($jobs as $j) if ($j instanceof Call) return $j;
        return null;
	}
	public function loadAllIdsForPartner(Partner $partner, $time)
	{
        $em = $this->getEntityManager();
        $query = $this->getEntityManager()->createQuery("
        	SELECT call, route, routeVersion, task, taskVersion, campaign, campaignVersion
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id 
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			JOIN ModelBundle:Task task WITH routeVersion.task = task.id
			JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id 
			JOIN ModelBundle:Campaign campaign WITH taskVersion.campaign = campaign.id
			JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id 
			WHERE campaignVersion.owner = :partner AND call.created_at > :from 
			ORDER BY call.created_at ASC")
        ->setParameter('partner', $partner)
        ->setParameter('from', (new \DateTime())->setTimestamp($time));
        $data = $query->getResult();
        $result = [];
        foreach($data as $object) if ($object instanceof Call) $result[] = $object->getId();
        return $result;
	}
	public function loadOneForMaster(Master $master, $id)
	{	
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT call, route, routeVersion, phone, phoneVersion
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id 
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id 
			JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id 
			WHERE phoneVersion.owner = :master AND call.id = :id");
        $query->setParameter('master', $master->getUser())->setParameter('id', $id);
        $jobs = $query->getResult();
        foreach ($jobs as $j) if ($j instanceof Call) return $j;
        return null;
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
			ORDER BY call.created_at ASC");
        $query->setParameter('master', $master->getUser())->setParameter('from', (new \DateTime())->setTimestamp($time));
        $data = $query->getResult();
        $result = [];
        foreach($data as $object) if ($object instanceof Call) $result[] = $object->getId();
        return $result;
	}
	public function loadLatestForTerminator(Dongle $terminator)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT call
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id 
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			WHERE routeVersion.terminator = :terminator
			ORDER BY call.created_at ASC");
		$query->setParameter("terminator", $terminator->getId());
		$result = $query->getResult();
		foreach($result as $r) if ($r instanceof Call) return $r;
		return null;
	}
	public function loadAllForRoute(Route $route)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT call, route, routeVersion, phone, phoneVersion
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id 
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id 
			JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id 
			WHERE route.id = :route
			ORDER BY call.created_at ASC");
		$query->setParameter("route", $route->getId());
		$result = $query->getResult();
		$calls = [];
		foreach($result as $r) if ($r instanceof Call) $calls[] = $r;
		return $calls;
	}
	public function loadByMaster(Master $master)
	{
		$query = $this->getEntityManager()->createQuery("
			SELECT call, route, routeVersion, phone, phoneVersion
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id 
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id 
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id 
			JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id 
			WHERE phoneVersion.owner = :owner
			ORDER BY call.created_at ASC");
		$query->setParameter('owner', $master->getUser());
		$jobs = $query->getResult();
		$result = [];
		foreach ($jobs as $j) if ($j instanceof Call) $result[] = $j;
		return $result;
	}
	public function loadUnbilledCalls()
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT call, route, routeVersion, phone, phoneVersion, user, userVersion, account
			FROM ModelBundle:Call call
			JOIN ModelBundle:Route route WITH call.route = route.id
			JOIN ModelBundle:RouteVersion routeVersion WITH route.actual = routeVersion.id
			JOIN ModelBundle:Phone phone WITH routeVersion.master_phone = phone.id
			JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id
			JOIN ModelBundle:User user WITH phoneVersion.owner = user.id
			JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
			JOIN ModelBundle:Account account WITH userVersion.main_account = account.id
			WHERE call.direction IN (:dirs) AND call.transaction IS NULL AND routeVersion.expired_at > :now");
		$query->setParameter('dirs', [ Call::DIRECTION_RG, Call::DIRECTION_RRG ]);
		$query->setParameter('now', new \DateTime("now"));
		$result = $query->getResult();
		$calls = [];
		foreach($result as $r)if ($r instanceof Call) $calls[] = $r;
		return $calls;
	}
}