<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class AccountRepository extends EntityRepository
{
    public function loadOneForMaster(Master $master, $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT account
            FROM ModelBundle:Account account
            WHERE account.owner = :master AND account.id = :id')
        ->setParameter('master', $master->getUser())
        ->setParameter('id', $id);
        $data = $query->getResult();
        foreach($data as $object) if ($object instanceof Account) return $object;
        return null;
    }
    public function loadAllIdsForMaster(Master $master, $time)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT account 
            FROM ModelBundle:Account account
            WHERE account.id = :id")
        ->setParameter('id', $master->getUser()->getMainAccount());
        $jobs = $query->getResult();
        foreach ($jobs as $j) if ($j instanceof Account) return [ $j->getId() ];
        return [];
        /*$em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT transactionEntry, transaction, toAccount
            FROM ModelBundle:TransactionEntry transactionEntry
            JOIN ModelBundle:Transaction transaction WITH transactionEntry.transaction = transaction.id
            JOIN ModelBundle:Account fromAccount WITH transactionEntry.acc_from = fromAccount.id
            JOIN ModelBundle:Account toAccount WITH transactionEntry.acc_to = toAccount.id
            WHERE (fromAccount.id = :id OR toAccount.id = :id) AND transaction.open_at > :from")
        ->setParameter('id', $master->getUser()->getMainAccount())
        ->setParameter('from', (new \DateTime())->setTimestamp($time));
        $jobs = $query->getResult();
        foreach ($jobs as $j) if ($j instanceof Account) return [ $j->getId() ];
        return [];*/
    }
}