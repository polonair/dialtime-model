<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Manager;
use Polonairs\Dialtime\ModelBundle\Entity\User;

class ManagerRepository extends EntityRepository
{
    private static function normalizeUsername($username)
    {
        if ($username === null) return null;
        $phone = str_replace(["+", "-", "(", ")", " ", ".", "/", "\\", "*"], "", $username);
        if (preg_match("#[78]?(9[0-9]{9})#", $phone, $matches)) return "7".$matches[1];
        return $username;
    }
    public function loadManagerByUser(User $user)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT manager
            FROM ModelBundle:Manager manager
            JOIN ModelBundle:ManagerVersion managerVersion WITH manager.actual = managerVersion.id
            WHERE manager.user = :user");
        $query->setParameter("user", $user);
        $result = $query->getResult();
        $manager = null;
        foreach($result as $r) if ($r instanceof Manager) { $manager = $r; break; }
        return $manager;
    }
    public function loadUser($username, $password)
    {
        $em = $this->getEntityManager();
        $normalized = ManagerRepository::normalizeUsername($username);
        $query = $em->createQuery("
            SELECT manager, user, userVersion
            FROM ModelBundle:Manager manager
            JOIN ModelBundle:User user WITH manager.user = user.id
            JOIN ModelBundle:UserVersion userVersion WITH user.actual = userVersion.id
            WHERE userVersion.username = :name");
        $query->setParameter("name", $normalized);
        $result = $query->getResult();
        $manager = null;
        foreach($result as $r) if ($r instanceof Manager) { $manager = $r; break; }
        if ($manager !== null && password_verify($password, $manager->getPassword())) return $manager;
        return null;
    }
}