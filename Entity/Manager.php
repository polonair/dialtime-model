<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\ManagerRepository")
 * @ORM\Table(name="managers")
 */
class Manager implements UserInterface, EquatableInterface
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/**
	 * @ORM\OneToOne(targetEntity="User")
	 */
    public $user;

	/** !required
	 * @ORM\OneToOne(targetEntity="ManagerVersion", cascade={"persist"})
	 */
    public $actual;

    /** !required
     * @ORM\Column(type="datetime")
     */
    public $created_at;

	/** !required
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    public $removed_at = null;

    public function __construct()
    {
    	$this->actual = new ManagerVersion();
    	$this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function getId()
    {
        return $this->id;
    }
    public function setUser(User $value)
    {
    	$this->user = $value;
    	return $this;
    }
    public function getUser()
    {
        return $this->user;
    }
    public function getRoles()
    {
    	return ["ROLE_USER"];
    }
    public function getPassword()
    {
    	return $this->user->getPassword();
    }
    public function getUsername()
    {
    	return $this->user->getUsername();
    }
    public function getSalt()
    {
    	return null;
    }
    public function eraseCredentials(){}
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) return false;
        if ($this->getPassword !== $user->getPassword()) return false;
        if ($this->getSalt !== $user->getSalt()) return false;
        if ($this->getUsername !== $user->getUsername()) return false;
        return true;
    }
}
