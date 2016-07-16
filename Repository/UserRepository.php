<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Admin;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;

class UserRepository extends EntityRepository
{
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
