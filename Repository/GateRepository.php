<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Gate;

class GateRepository extends EntityRepository
{
	public function loadAllIndexed()
	{
		$em = $this->getEntityManager();
		$query = $em->createQuery("
			SELECT gate, gateVersion
			FROM ModelBundle:Gate gate
			JOIN ModelBundle:GateVersion gateVersion WITH gate.actual = gateVersion.id");
		$result = $query->getResult();
		$gates = [];
		foreach($result as $r) if ($r instanceof Gate) $gates[$r->getId()] = $r;
		return $gates;
	}
}