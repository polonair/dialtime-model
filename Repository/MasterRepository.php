<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Service\UserService\UserService;
use Polonairs\Dialtime\ModelBundle\Entity\Master;

class MasterRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        $em = $this->getEntityManager();
        $normalized = UserService::normalizeUsername($username);
        $query = $em->createQuery("
            SELECT master, user, userVersion
            FROM ModelBundle:Master master
            JOIN ModelBundle:User user WITH master.user = user.id
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
            WHERE userVersion.username = :name");
        $query->setParameter("name", $normalized);
        $result = $query->getResult();
        $master = null;
        foreach($result as $r) if ($r instanceof Master) { $master = $r; break; }
        if ($master !== null) return $master;
        throw new UsernameNotFoundException("Unable to find an active user \"$username\".");        
    }
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) 
        {
            throw new UnsupportedUserException('Instances of "'.$class.'" are not supported.');
        }
        $uid = $user->getId();
        $user = $this->getEntityManager()->getRepository("ModelBundle:Master")->find($uid);
        return $user;
    }
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}