<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Campaign;

class CampaignRepository extends EntityRepository
{
    public function loadActive()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c, cv, m, u, uv, a FROM ModelBundle:Campaign c JOIN ModelBundle:CampaignVersion cv WITH c.actual = cv.id JOIN ModelBundle:Partner m WITH cv.owner = m.id JOIN ModelBundle:User u WITH u.id = m.user JOIN ModelBundle:UserVersion uv WITH uv.id = u.actual JOIN ModelBundle:Account a WITH a.id = uv.main_account WHERE cv.state = :state1');
        $query->setParameter('state1', Campaign::STATE_ACTIVE);
        $campaigns = $query->getResult();
        $result = [];
        for ($i = 0; $i < count($campaigns); $i++) if ($campaigns[$i] instanceof Campaign) $result[$campaigns[$i]->getId()] = $campaigns[$i];
        //dump($campaigns);
        //dump($result);
        return $result;
    }
	public function loadActiveForPartner(Partner $partner)
	{
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM ModelBundle:Campaign c JOIN ModelBundle:CampaignVersion cv WITH c.actual = cv.id WHERE cv.owner = :owner');
        $query->setParameter('owner', $partner->getId());
        $campaigns = $query->getResult();
        return $campaigns;
	}
	public function loadOneById($id, Partner $partner = null)
	{
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c,cv FROM ModelBundle:Campaign c JOIN ModelBundle:CampaignVersion cv WITH c.actual = cv.id WHERE cv.owner = :owner AND c.id = :id');
        $query->setParameter('owner', $partner->getId());
        $query->setParameter('id', $id);
        $campaigns = $query->getResult();
        return $campaigns[0];
	}
}