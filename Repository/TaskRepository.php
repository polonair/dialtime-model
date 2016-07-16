<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Task;
use Polonairs\Dialtime\ModelBundle\Entity\Dongle;

class TaskRepository extends EntityRepository
{
    public function loadTaskForOriginator(Dongle $originator)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT dongle, dongleVersion, campaign, campaignVersion, taskVersion, task
            FROM ModelBundle:Dongle dongle
            JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
            JOIN ModelBundle:Campaign campaign WITH dongleVersion.campaign = campaign.id
            JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id
            JOIN ModelBundle:TaskVersion taskVersion WITH campaign.id = taskVersion.campaign
            JOIN ModelBundle:Task task WITH taskVersion.id = task.actual
            WHERE dongle.id = :originator AND taskVersion.state = :state");
        $query
            ->setParameter("originator", $originator->getId())
            ->setParameter("state", Task::STATE_ACTIVE);
        $result = $query->getResult();
        foreach ($result as $r) if ($r instanceof Task) return $r;
        return null;
    }
	public function loadOpenedTasksMatrix()
	{
		return [[]];
	}
	public function loadOpenedTasks()
	{
		return [];
	}
	public function loadActive()
	{
		$query = $this->getEntityManager()->createQuery("
            SELECT task, taskVersion, campaign, campaignVersion, offer, offerVersion, phone, phoneVersion
            FROM ModelBundle:Task task 
            JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id 
            JOIN ModelBundle:Campaign campaign WITH taskVersion.campaign = campaign.id
            JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id
            JOIN ModelBundle:Offer offer WITH taskVersion.offer = offer.id
            JOIN ModelBundle:OfferVersion offerVersion WITH offer.actual = offerVersion.id
            JOIN ModelBundle:Phone phone WITH offerVersion.phone = phone.id
            JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id
            WHERE taskVersion.state = :state");
		$query->setParameter("state", Task::STATE_ACTIVE);
        $jobs = $query->getResult();
		$result = [];
		foreach ($jobs as $j) if ($j instanceof Task) $result[] = $j;
		return $result;
	}
    public function loadMatrix()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT s, sv, l, c FROM ModelBundle:Task s JOIN ModelBundle:TaskVersion sv WITH s.actual = sv.id JOIN ModelBundle:Campaign c WITH c.id = sv.campaign JOIN ModelBundle:Offer l WITH l.id = sv.offer WHERE sv.state = :state');
        $query->setParameter("state", Task::STATE_ACTIVE);
        $tasks = $query->getResult();
        $result = [];
        for ($i = 0; $i < count($tasks); $i++) 
        {
            //dump($tasks[$i]);
            if ($tasks[$i] instanceof Task)
            {
                $result[$tasks[$i]->getCampaign()->getId()][$tasks[$i]->getOffer()->getId()] = $tasks[$i];
            }
        }
        //dump($campaigns);
        //dump($result);
        return $result;
    }
}