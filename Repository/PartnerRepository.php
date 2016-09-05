<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Partner;

class PartnerRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{
    private static function normalizeUsername($username)
    {
        if ($username === null) return null;
        $phone = str_replace(["+", "-", "(", ")", " ", ".", "/", "\\", "*"], "", $username);
        if (preg_match("#[78]?(9[0-9]{9})#", $phone, $matches)) return "7".$matches[1];
        return $username;
    }
    public function loadUserByUsername($username)
    {
        $em = $this->getEntityManager();
        $normalized = PartnerRepository::normalizeUsername($username);
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