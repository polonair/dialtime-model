<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Call;
use Polonairs\Dialtime\ModelBundle\Entity\Route;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\Dongle;

class CallRepository extends EntityRepository
{
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