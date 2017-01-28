<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\Admin;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Polonairs\Dialtime\ModelBundle\Entity\Manager;

class UserRepository extends EntityRepository
{
    public function loadMyClient(Manager $manager, $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT user, userVersion, master, masterVersion, partner, partnerVersion
            FROM ModelBundle:User user
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
            LEFT JOIN ModelBundle:Master master WITH user.id = master.user
            LEFT JOIN ModelBundle:MasterVersion masterVersion WITH master.actual = masterVersion.id
            LEFT JOIN ModelBundle:Partner partner WITH user.id = partner.user
            LEFT JOIN ModelBundle:PartnerVersion partnerVersion WITH partner.actual = partnerVersion.id
            WHERE user.id = :id AND (masterVersion.manager = :manager OR  partnerVersion.manager = :manager)");
        $query->setParameter("manager", $manager);
        $query->setParameter("id", $id);
        $result = $query->getResult();
        $return = [ "user" => null, "master" => null, "partner" => null ];
        foreach($result as $r)
        {
            if ($r instanceof Master) $return["master"] = $r;
            elseif  ($r instanceof Partner) $return["partner"] = $r;
            elseif  ($r instanceof User) $return["user"] = $r;
        }
        return $return;
    }
    public function loadAllMyClientsIdsForManager(Manager $manager, $time)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT user, userVersion, master, masterVersion, partner, partnerVersion
            FROM ModelBundle:User user
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
            LEFT JOIN ModelBundle:Master master WITH user.id = master.user
            LEFT JOIN ModelBundle:MasterVersion masterVersion WITH master.actual = masterVersion.id
            LEFT JOIN ModelBundle:Partner partner WITH user.id = partner.user
            LEFT JOIN ModelBundle:PartnerVersion partnerVersion WITH partner.actual = partnerVersion.id
            WHERE masterVersion.manager = :manager OR  partnerVersion.manager = :manager");
        $query->setParameter("manager", $manager);
        $result = $query->getResult();
        $return = [ ];
        foreach($result as $r)
        {
            if ($r instanceof User) $return[] = $r->getId();
        }
        return $return;
    }
    public function loadFreeClient(Manager $manager, $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT user, userVersion, master, partner
            FROM ModelBundle:User user
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
            LEFT JOIN ModelBundle:Master master WITH user.id = master.user
            LEFT JOIN ModelBundle:Partner partner WITH user.id = partner.user
            WHERE user.id = :id");
        $query->setParameter("id", $id);
        $result = $query->getResult();
        $return = [ "user" => null, "master" => null, "partner" => null ];
        foreach($result as $r)
        {
            if ($r instanceof Master) $return["master"] = $r;
            elseif  ($r instanceof Partner) $return["partner"] = $r;
            elseif  ($r instanceof User) $return["user"] = $r;
        }
        return $return;
    }
    public function loadNewClients()
    {
        $em = $this->getEntityManager();
        $clients = [];
        $query = $em->createQuery("
            SELECT master, masterVersion, user, userVersion
            FROM ModelBundle:Master master
            JOIN ModelBundle:MasterVersion masterVersion WITH master.actual = masterVersion.id
            JOIN ModelBundle:User user WITH master.user = user.id
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id            
            WHERE masterVersion.manager IS NULL")->setMaxResults(5);
        $result = $query->getResult();
        foreach($result as $r) if ($r instanceof User) { $clients[$r->getId()] = $r; }
        dump($clients);

        $query = $em->createQuery("
            SELECT partner, partnerVersion, user, userVersion
            FROM ModelBundle:Partner partner
            JOIN ModelBundle:PartnerVersion partnerVersion WITH partner.actual = partnerVersion.id
            JOIN ModelBundle:User user WITH partner.user = user.id
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id            
            WHERE partnerVersion.manager IS NULL")->setMaxResults(5);
        $result = $query->getResult();
        foreach($result as $r) if ($r instanceof User) { $clients[$r->getId()] = $r; }
        dump($clients);
        $result = [];
        foreach($clients as $key => $value) $result[] = $key;
        dump($result);
        return $result;
    }
    public function loadUserRoles($username)
    {
    	$em = $this->getEntityManager();
    	$query = $em->createQuery("
    		SELECT user, userVersion, admin, master, partner
    		FROM ModelBundle:User user
    		JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
    		LEFT JOIN ModelBundle:Admin admin WITH user.id = admin.user
    		LEFT JOIN ModelBundle:Master master WITH user.id = master.user
    		LEFT JOIN ModelBundle:Partner partner WITH user.id = partner.user
    		WHERE userVersion.username = :username");
    	$query->setParameter("username", $username);
    	$result = $query->getResult();
    	$roles = [];
    	foreach($result as $r)
    	{
    		if ($r instanceof Admin) $roles["admin"] = $r;
    		elseif  ($r instanceof Master) $roles["master"] = $r;
    		elseif  ($r instanceof Partner) $roles["partner"] = $r;
    	}
    	return $roles;
    }
}
