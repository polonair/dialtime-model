<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Session;

class SessionRepository extends EntityRepository
{
	public function loadSession($auth_key, $realm)
	{
		if (strlen($auth_key) < 32) return null;
		$em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT session
            FROM ModelBundle:Session session
            WHERE session.id = :id AND session.realm = :realm AND session.created_at < :now AND session.closed_at > :now');
        $query
        	->setParameter('id', $auth_key)
        	->setParameter('realm', $realm)
        	->setParameter('now', new \DateTime('now'));
        $data = $query->getResult();
        if (count($data) < 1) return null;
        return $data[0];
	}
}
