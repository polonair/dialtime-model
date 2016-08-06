<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Transaction;
use Polonairs\Dialtime\ModelBundle\Entity\User;
use Polonairs\Dialtime\ModelBundle\Entity\TransactionEntry;

class TransactionRepository extends EntityRepository
{
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
}