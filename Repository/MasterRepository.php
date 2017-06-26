<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Master;
use Polonairs\Dialtime\ModelBundle\Entity\User;

class MasterRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{
    private static function normalizeUsername($username)
    {
        if ($username === null) return null;
        $phone = str_replace(["+", "-", "(", ")", " ", ".", "/", "\\", "*"], "", $username);
        if (preg_match("#[78]?(9[0-9]{9})#", $phone, $matches)) return "7".$matches[1];
        return $username;
    }
    public function loadMasterByUser(User $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT master
            FROM ModelBundle:Master master
            JOIN ModelBundle:MasterVersion masterVersion WITH master.actual = masterVersion.id
            WHERE master.user = :user");
        $query->setParameter("user", $user);
        $result = $query->getResult();
        $master = null;
        foreach($result as $r) if ($r instanceof Master) { $master = $r; break; }
        return $master;
    }
    public function loadUser($username, $password)
    {
        $em = $this->getEntityManager();
        $normalized = MasterRepository::normalizeUsername($username);
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
        if ($master !== null && password_verify($password, $master->getPassword())) return $master;
        return null;
    }
    public function loadUserByUsername($username)
    {
        $em = $this->getEntityManager();
        $normalized = MasterRepository::normalizeUsername($username);
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
    public function loadAllMasterPhones()
    {
        $result = $this->getEntityManager()->createQuery("
            SELECT master, user, userVersion, phone, phoneVersion
            FROM ModelBundle:Master master
            JOIN ModelBundle:User user WITH master.user = user.id
            JOIN ModelBundle:Phone phone WITH user.id = phone.owner
            JOIN ModelBundle:PhoneVersion phoneVersion WITH phone.actual = phoneVersion.id
            WHERE 1")
            ->getResult();
        $phones = [];
        foreach($result as $r) if ($r instanceof Phone) $phones[] = $r->getNumber();
        return $phones;
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