<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\Schedule;
use Polonairs\Dialtime\ModelBundle\Entity\Interval;

class ScheduleRepository extends EntityRepository
{
	public function loadByOwner(User $owner)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT schedule, scheduleVersion, interval
			FROM ModelBundle:Schedule schedule
			JOIN ModelBundle:ScheduleVersion scheduleVersion WITH schedule.actual = scheduleVersion.id
			JOIN ModelBundle:Interval interval WITH schedule.id = interval.schedule
			WHERE scheduleVersion.owner = :owner");
		$query->setParameter("owner", $owner->getId());
		$result = $query->getResult();
		$schedules = [];
		foreach($result as $r)
		{
			if ($r instanceof Schedule) $schedules[$r->getId()] = $r;
		}
		foreach($result as $r)
		{
			if ($r instanceof Interval) $schedules[$r->getSchedule()->getId()]->addInterval($r);
		}
		//dump($schedules);
		return $schedules;
	}
}