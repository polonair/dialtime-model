<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TransactionEntryRepository extends EntityRepository
{
	public function apply($entry)
	{
		$em = $this->getEntityManager();
		$em->getConnection()->beginTransaction();
		$entry = $em->getRepository("ModelBundle:TransactionEntry")->find($entry->getId());
		$entries = $em->getRepository("ModelBundle:TransactionEntry")->findByTransaction($entry->getTransaction()->getId());
		$entry->setDone(true);
		$allDone = true;
		foreach($entries as $e)
		{
			if($e->getDone() === false)
			{
				$allDone = false;
				break;
			}
		}
		if ($allDone)
		{
			foreach($entries as $e)
			{
				$e->getFrom()->decreaseBalance($e->getAmount());
				$e->getTo()->increaseBalance($e->getAmount());
			}
			$entry->getTransaction()->close();
		}
		$em->flush();
		$em->getConnection()->commit();
	}
}