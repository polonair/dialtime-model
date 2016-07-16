<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\ServerJob;

class ServerJobRepository extends EntityRepository
{
	public function loadJobsForServerId($serverid)
	{
		$query = $this->getEntityManager()->createQuery(
			"SELECT sj, sjv FROM ModelBundle:ServerJob sj JOIN ModelBundle:ServerJobVersion sjv WITH sj.actual = sjv.id WHERE sjv.server = :servid ORDER BY sjv.position ASC");
		$query->setParameter('servid', $serverid);
		$jobs = $query->getResult();
		$result = [];
		foreach ($jobs as $j) if ($j instanceof ServerJob) $result[] = $j;
		return $result;
	}
}