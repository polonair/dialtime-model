<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\Phone;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PhoneRepository extends EntityRepository
{
    public function loadByOwner(User $master)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
        	SELECT phone
        	FROM ModelBundle:Phone phone 
        	JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id 
        	WHERE phoneVersion.owner = :owner");
        $query->setParameter('owner', $master->getId());
        $phones = $query->getResult();
        return $phones;
    }
	public function loadByNumber($number)
	{
        $em = $this->getEntityManager();
        $query = $em->createQuery("
        	SELECT phone, phoneVersion
        	FROM ModelBundle:Phone phone 
        	JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id 
        	WHERE phoneVersion.number = :number");
        $query->setParameter('number', $number);
		$data = $query->getResult();
		foreach($data as $phone) if ($phone instanceof Phone) return $phone;
		return null;
	}
}