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
use Polonairs\Dialtime\ModelBundle\Entity\Partner;

class PartnerRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{
    /*public function loadUserByUsername($username)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT c FROM ModelBundle:Partner c JOIN ModelBundle:User u WITH c.user = u.id JOIN ModelBundle:UserVersion v WITH u.actual = v.id WHERE v.username = :name');
        $query->setParameter('name', $username);
        $admin = $query->getResult();
        if (count( $admin) < 1)
        {
            throw new UsernameNotFoundException('Unable to find an active user "'.$username.'".');
        }
        return $admin[0];
    }*/
    public function loadUserByUsername($username)
    {
        $em = $this->getEntityManager();
        $normalized = UserService::normalizeUsername($username);
        $query = $em->createQuery("
            SELECT partner, user, userVersion
            FROM ModelBundle:Partner partner
            JOIN ModelBundle:User user WITH partner.user = user.id
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
            WHERE userVersion.username = :name");
        $query->setParameter("name", $normalized);
        $result = $query->getResult();
        $partner = null;
        foreach($result as $r) if ($r instanceof Partner) { $partner = $r; break; }
        if ($partner !== null) return $partner;
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
        $user = $this->getEntityManager()->getRepository("ModelBundle:Partner")->find($uid);
        return $user;
    }
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}