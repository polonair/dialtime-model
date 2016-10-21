<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Offer;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Polonairs\Dialtime\ModelBundle\Entity\Schedule;
use Polonairs\Dialtime\ModelBundle\Entity\Interval;
use Polonairs\Dialtime\ModelBundle\Entity\Task;

class OfferRepository extends EntityRepository
{
    public function loadActive()
    {
        $offers = $this
            ->getEntityManager()
            ->createQuery("
                SELECT 
                    offer, 
                    offerVersion, 
                    master, 
                    user, 
                    userVersion, 
                    account,
                    schedule,
                    scheduleVersion
                FROM ModelBundle:Offer offer 
                JOIN ModelBundle:OfferVersion offerVersion WITH offer.actual = offerVersion.id 
                JOIN ModelBundle:Master master WITH offerVersion.owner = master.id 
                JOIN ModelBundle:User user WITH user.id = master.user 
                JOIN ModelBundle:UserVersion userVersion WITH userVersion.id = user.actual 
                JOIN ModelBundle:Account account WITH account.id = userVersion.main_account
                JOIN ModelBundle:Schedule schedule WITH schedule.id = offerVersion.schedule  
                JOIN ModelBundle:ScheduleVersion scheduleVersion WITH scheduleVersion.id = schedule.actual
                WHERE offerVersion.state IN (:states)")
            ->setParameter('states', [Offer::STATE_ON, Offer::STATE_AUTO])
            ->getResult();
        $result = [];
        foreach($offers as $offer) if ($offer instanceof Offer) $result[$offer->getId()] = $offer;
        return $result;
    }
    public function loadAllForMaster(Master $master)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT offer, 
                offerVersion, 
                category, 
                categoryVersion, 
                location, 
                locationVersion, 
                phone, 
                phoneVersion, 
                schedule, 
                scheduleVersion, 
                interval, 
                task, 
                taskVersion
            FROM ModelBundle:Offer offer 
            JOIN ModelBundle:OfferVersion offerVersion WITH offer.actual = offerVersion.id
            JOIN ModelBundle:Category category WITH offerVersion.category = category.id
            JOIN ModelBundle:CategoryVersion categoryVersion WITH category.actual = categoryVersion.id
            JOIN ModelBundle:Location location WITH offerVersion.location = location.id
            JOIN ModelBundle:LocationVersion locationVersion WITH location.actual = locationVersion.id
            JOIN ModelBundle:Phone phone WITH offerVersion.phone = phone.id
            JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id
            JOIN ModelBundle:Schedule schedule WITH offerVersion.schedule = schedule.id
            JOIN ModelBundle:ScheduleVersion scheduleVersion WITH offer.actual = offerVersion.id
            JOIN ModelBundle:Interval interval WITH schedule.id = interval.schedule
            LEFT OUTER JOIN ModelBundle:TaskVersion taskVersion WITH taskVersion.offer = offer.id
            LEFT OUTER JOIN ModelBundle:Task task WITH taskVersion.entity = task.id AND task.actual = taskVersion.id
            WHERE offerVersion.owner = :master AND
                taskVersion.state = :state');
        $query->setParameter('master', $master);
        $query->setParameter('state', Task::STATE_ACTIVE);
        $data = $query->getResult();
        $result = [];
        $schedules = [];
        foreach($data as $object) 
        {
            if ($object instanceof Offer) $result[$object->getId()] = $object;            
            if ($object instanceof Schedule) $schedules[$object->getId()] = $object;
        }
        foreach($data as $object) 
        {
            if ($object instanceof Interval) $schedules[$object->getSchedule()->getId()]->addInterval($object);
        }
        foreach($data as $object) 
        {
            if ($object instanceof Task) 
            {
                if ($result[$object->getOffer()->getId()]->getTask() === null)
                {
                    $result[$object->getOffer()->getId()]->setTask($object);
                }
                else 
                {
                    if ($result[$object->getOffer()->getId()]->getTask()->getRate() < $object->getRate())
                    {
                        $result[$object->getOffer()->getId()]->setTask($object);
                    }
                }
            }
        }

        return $result;
    }
    public function isOfferActual(Offer $offer, $value)
    {
        $count = $this
            ->getEntityManager()
            ->createQuery('
                SELECT COUNT(interval.id)
                FROM ModelBundle:Interval interval
                JOIN ModelBundle:Schedule schedule WITH interval.schedule = schedule.id
                JOIN ModelBundle:ScheduleVersion scheduleVersion WITH schedule.actual = scheduleVersion.id
                JOIN ModelBundle:OfferVersion offerVersion WITH schedule.id = offerVersion.schedule
                JOIN ModelBundle:Offer offer WITH offerVersion.entity = offer.id AND offerVersion.id = offer.actual
                WHERE offer.id = :id AND interval.from_time < :value AND interval.to_time > :value')
            ->setParameter('id', $offer->getId())
            ->setParameter('value', $value)
            ->getSingleScalarResult();
        return ($count > 0);
    }
}