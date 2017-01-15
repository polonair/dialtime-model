<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Task;
use Polonairs\Dialtime\ModelBundle\Entity\Dongle;
use Polonairs\Dialtime\ModelBundle\Entity\Master;

class TaskRepository extends EntityRepository
{
    public function loadOneForMaster($master, $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT task, taskVersion, offer, offerVersion
            FROM ModelBundle:Task task 
            JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id
            JOIN ModelBundle:Offer offer WITH offer.id = taskVersion.offer
            JOIN ModelBundle:OfferVersion offerVersion WITH offer.actual = offerVersion.id
            WHERE offerVersion.owner = :master AND task.id = :id');
        $query->setParameter('master', $master)->setParameter('id', $id);
        $data = $query->getResult();
        $result = null;

        foreach($data as $object) 
        {
            if ($object instanceof Task) 
            {
                $result = $object;
                break;
            }
        }

        return $result;        
    }
    public function loadAllIdsForMaster(Master $master, $time)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT task, taskVersion, offer, offerVersion
            FROM ModelBundle:Task task 
            JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id
            JOIN ModelBundle:Offer offer WITH offer.id = taskVersion.offer
            JOIN ModelBundle:OfferVersion offerVersion WITH offer.actual = offerVersion.id
            WHERE offerVersion.owner = :master AND taskVersion.created_at > :from AND taskVersion.state = :state');
        $query->setParameter('master', $master)->setParameter('from', (new \DateTime())->setTimestamp($time))->setParameter('state', Task::STATE_ACTIVE);
        $data = $query->getResult();
        $result = [];
        foreach($data as $object) if ($object instanceof Task) $result[] = $object->getId();
        return $result;
    }
    public function loadTaskForOriginator(Dongle $originator)
    {
        $result = $this
            ->getEntityManager()
            ->createQuery("
                SELECT dongle, 
                    dongleVersion, 
                    campaign, 
                    campaignVersion, 
                    taskVersion, 
                    task
                FROM ModelBundle:Dongle dongle
                JOIN ModelBundle:DongleVersion dongleVersion WITH dongle.actual = dongleVersion.id
                JOIN ModelBundle:Campaign campaign WITH dongleVersion.campaign = campaign.id
                JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id
                JOIN ModelBundle:TaskVersion taskVersion WITH campaign.id = taskVersion.campaign
                JOIN ModelBundle:Task task WITH taskVersion.id = task.actual
                WHERE dongle.id = :originator AND taskVersion.state = :state")
            ->setParameter("originator", $originator->getId())
            ->setParameter("state", Task::STATE_ACTIVE)
            ->getResult();
        foreach ($result as $r) if ($r instanceof Task) return $r;
        return null;
    }
	/*public function loadOpenedTasksMatrix()
	{
		return [[]];
	}
	public function loadOpenedTasks()
	{
		return [];
	}*/
	public function loadActive()
	{
        $jobs = $this
            ->getEntityManager()
            ->createQuery("
                SELECT task, 
                    taskVersion, 
                    campaign, 
                    campaignVersion, 
                    offer, 
                    offerVersion, 
                    phone, 
                    phoneVersion
                FROM ModelBundle:Task task 
                JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id 
                JOIN ModelBundle:Campaign campaign WITH taskVersion.campaign = campaign.id
                JOIN ModelBundle:CampaignVersion campaignVersion WITH campaign.actual = campaignVersion.id
                JOIN ModelBundle:Offer offer WITH taskVersion.offer = offer.id
                JOIN ModelBundle:OfferVersion offerVersion WITH offer.actual = offerVersion.id
                JOIN ModelBundle:Phone phone WITH offerVersion.phone = phone.id
                JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id
                WHERE taskVersion.state = :state
                ORDER BY taskVersion.rate DESC")
            ->setParameter("state", Task::STATE_ACTIVE)
            ->getResult();
		$result = [];
		foreach ($jobs as $j) if ($j instanceof Task) $result[] = $j;
		return $result;
	}
    public function loadMatrix()
    {
        $tasks = $this
            ->getEntityManager()
            ->createQuery('
                SELECT task, 
                    taskVersion, 
                    offer, 
                    campaign 
                FROM ModelBundle:Task task 
                JOIN ModelBundle:TaskVersion taskVersion WITH task.actual = taskVersion.id 
                JOIN ModelBundle:Campaign campaign WITH campaign.id = taskVersion.campaign 
                JOIN ModelBundle:Offer offer WITH offer.id = taskVersion.offer 
                WHERE taskVersion.state = :state')
            ->setParameter("state", Task::STATE_ACTIVE)
            ->getResult();
        $result = [];
        foreach($tasks as $task) if ($task instanceof Task)
            $result[$task->getCampaign()->getId()][$task->getOffer()->getId()] = $task;
        return $result;
    }
}