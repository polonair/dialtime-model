<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\Schedule;
use Polonairs\Dialtime\ModelBundle\Entity\Interval;
use Polonairs\Dialtime\ModelBundle\Entity\Master;

class ScheduleRepository extends EntityRepository
{
	public function loadOneForMaster(Master $master, $id)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT schedule, scheduleVersion, interval
			FROM ModelBundle:Schedule schedule
			JOIN ModelBundle:ScheduleVersion scheduleVersion WITH schedule.actual = scheduleVersion.id
			JOIN ModelBundle:Interval interval WITH schedule.id = interval.schedule
			WHERE scheduleVersion.owner = :owner AND schedule.id = :id AND interval.removed_at IS NULL");
		$query->setParameter("owner", $master->getUser()->getId())->setParameter('id', $id);
		$result = $query->getResult();
		$schedule = null;
		foreach($result as $r) if ($r instanceof Schedule) { $schedule = $r; break; }
		if ($schedule !== null)
		{
			foreach($result as $r) if ($r instanceof Interval) $schedule->addInterval($r);
		}
		return $schedule;
	}
	public function loadAllIdsForMaster(Master $master, $time)
	{
        $em = $this->getEntityManager();
        $query = $em->createQuery("
			SELECT schedule, scheduleVersion, interval
			FROM ModelBundle:Schedule schedule
			JOIN ModelBundle:ScheduleVersion scheduleVersion WITH schedule.actual = scheduleVersion.id
			JOIN ModelBundle:Interval interval WITH schedule.id = interval.schedule
			WHERE scheduleVersion.owner = :owner AND (scheduleVersion.created_at > :from OR interval.created_at > :from)");
        $query
        	->setParameter('owner', $master->getUser()->getId())
        	->setParameter('from', (new \DateTime())->setTimestamp($time));
        $data = $query->getResult();
        $result = [];
        foreach($data as $object) if ($object instanceof Schedule) $result[] = $object->getId();
        return $result;
	}
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
	public function loadById($owner)
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT schedule, scheduleVersion, interval
			FROM ModelBundle:Schedule schedule
			JOIN ModelBundle:ScheduleVersion scheduleVersion WITH schedule.actual = scheduleVersion.id
			JOIN ModelBundle:Interval interval WITH schedule.id = interval.schedule
			WHERE schedule.id = :owner");
		$query->setParameter("owner", $owner);
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
		return $schedules[$owner];
	}
}