<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Offer;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Polonairs\Dialtime\ModelBundle\Entity\Schedule;
use Polonairs\Dialtime\ModelBundle\Entity\Interval;

class OfferRepository extends EntityRepository
{
    public function loadActive()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT c, cv, m, u, uv, a FROM ModelBundle:Offer c JOIN ModelBundle:OfferVersion cv WITH c.actual = cv.id JOIN ModelBundle:Master m WITH cv.owner = m.id JOIN ModelBundle:User u WITH u.id = m.user JOIN ModelBundle:UserVersion uv WITH uv.id = u.actual JOIN ModelBundle:Account a WITH a.id = uv.main_account WHERE cv.state = :state1 OR cv.state = :state2");
        $query->setParameter('state1', Offer::STATE_ON);
        $query->setParameter('state2', Offer::STATE_AUTO);
        $offers = $query->getResult();
        $result = [];
        for ($i = 0; $i < count($offers); $i++) if ($offers[$i] instanceof Offer) $result[$offers[$i]->getId()] = $offers[$i];
        return $result;
    }
    public function loadAllForMaster(Master $master)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT offer, offerVersion, category, categoryVersion, location, locationVersion, phone, phoneVersion, schedule, scheduleVersion, interval
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
            WHERE offerVersion.owner = :master');
        $query->setParameter('master', $master);
        $data = $query->getResult();
        //dump($data); die;
        $result = [];
        $schedules = [];
        foreach($data as $object) 
        {
            if ($object instanceof Offer) $result[] = $object;            
            if ($object instanceof Schedule) $schedules[$object->getId()] = $object;
        }
        foreach($data as $object) 
        {
            if ($object instanceof Interval) $schedules[$object->getSchedule()->getId()]->addInterval($object);
        }

        return $result;
    }
}