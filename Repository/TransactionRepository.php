<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Transaction;
use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\TransactionEntry;

class TransactionRepository extends EntityRepository
{
    public function loadOneForMaster(Master $master, $id)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT transactionEntry, transaction, fromAccount, toAccount
            FROM ModelBundle:TransactionEntry transactionEntry
            JOIN ModelBundle:Transaction transaction WITH transactionEntry.transaction = transaction.id
            JOIN ModelBundle:Account fromAccount WITH transactionEntry.acc_from = fromAccount.id
            JOIN ModelBundle:Account toAccount WITH transactionEntry.acc_to = toAccount.id
            WHERE (fromAccount.owner = :user OR toAccount.owner = :user) AND transactionEntry.id = :id");
        $query->setParameter('user', $master->getUser())->setParameter('id', $id);
        $jobs = $query->getResult();
        foreach ($jobs as $j) if ($j instanceof TransactionEntry) return $j;
        return null;
    }
    public function loadAllIdsForMaster(Master $master, $time)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT transactionEntry, transaction, fromAccount, toAccount
            FROM ModelBundle:TransactionEntry transactionEntry
            JOIN ModelBundle:Transaction transaction WITH transactionEntry.transaction = transaction.id
            JOIN ModelBundle:Account fromAccount WITH transactionEntry.acc_from = fromAccount.id
            JOIN ModelBundle:Account toAccount WITH transactionEntry.acc_to = toAccount.id
            WHERE (fromAccount.owner = :user OR toAccount.owner = :user) AND transaction.open_at > :from");
        $query->setParameter('user', $master->getUser())->setParameter('from', (new \DateTime())->setTimestamp($time));
        $jobs = $query->getResult();
        $result = [];
        foreach ($jobs as $j) if ($j instanceof TransactionEntry) $result[] = $j->getId();
        return $result;
    }
    public function doHold(Transaction $transaction)
    {
    	$em = $this->getEntityManager();

    	if ($transaction->isOpen())
    	{
    		$entries = $em->getRepository("ModelBundle:TransactionEntry")->findByTransaction($transaction);
    		
    		$em->getConnection()->beginTransaction();

            //$transaction->setState(Transaction::STATE_HOLD);
            $transaction->hold();
    		foreach($entries as $entry)
    		{
    			$amount = $entry->getAmount();
    			$from = $entry->getFrom();
    			$to = $entry->getTo();
    			$from->setBalance($from->getBalance()-$amount)->setOutcomeHold($from->getOutcomeHold()+$amount);
    			$to->setIncomeHold($to->getIncomeHold()+$amount);
    		}

    		$em->persist($transaction);
    		$em->flush();
    		$em->getConnection()->commit();
    	}
    }
    public function doApply(Transaction $transaction)
    {
        $em = $this->getEntityManager();

        if ($transaction->isHold())
        {
            $entries = $em->getRepository("ModelBundle:TransactionEntry")->findByTransaction($transaction);
            
            $em->getConnection()->beginTransaction();

            $transaction->close();
            foreach($entries as $entry)
            {
                $amount = $entry->getAmount();
                $from = $entry->getFrom();
                $to = $entry->getTo();
                $from->setOutcomeHold($from->getOutcomeHold() - $amount);
                $to->setBalance($to->getBalance() + $amount)->setIncomeHold($to->getIncomeHold() - $amount);
            }

            $em->persist($transaction);
            $em->flush();
            $em->getConnection()->commit();
        }       
    }
    public function doCancel(Transaction $transaction)
    {
        $em = $this->getEntityManager();

        if ($transaction->isHold())
        {
            $entries = $em->getRepository("ModelBundle:TransactionEntry")->findByTransaction($transaction);
            
            $em->getConnection()->beginTransaction();

            $transaction->cancel();
            foreach($entries as $entry)
            {
                $amount = $entry->getAmount();
                $from = $entry->getFrom();
                $to = $entry->getTo();
                $from->setBalance($from->getBalance()+$amount)->setOutcomeHold($from->getOutcomeHold()-$amount);
                $to->setIncomeHold($to->getIncomeHold()-$amount);
            }

            $em->persist($transaction);
            $em->flush();
            $em->getConnection()->commit();
        }       
    }
    public function loadAllForUser(User $user)
    {
    	$em = $this->getEntityManager();

		$query = $em->createQuery("
			SELECT transactionEntry, transaction, fromAccount, toAccount
			FROM ModelBundle:TransactionEntry transactionEntry
			JOIN ModelBundle:Transaction transaction WITH transactionEntry.transaction = transaction.id
			JOIN ModelBundle:Account fromAccount WITH transactionEntry.acc_from = fromAccount.id
			JOIN ModelBundle:Account toAccount WITH transactionEntry.acc_to = toAccount.id
			WHERE fromAccount.owner = :user OR toAccount.owner = :user");
		$query->setParameter('user', $user->getId());
		$jobs = $query->getResult();
		$result = [];
		foreach ($jobs as $j) if ($j instanceof TransactionEntry) $result[] = $j;
		return $result;
    }
    public function loadHeld()
    {
        $data = $this
            ->getEntityManager()
            ->createQuery("
                SELECT transaction
                FROM ModelBundle:Transaction transaction
                WHERE transaction.open_at IS NOT NULL AND 
                    transaction.hold_at IS NOT NULL AND 
                    transaction.cancel_at IS NULL AND 
                    transaction.close_at IS NULL")
            ->getResult();
        $result = [];
        foreach ($data as $d) if ($d instanceof Transaction) $result[] = $d;
        return $result;
    }
}