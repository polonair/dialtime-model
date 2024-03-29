<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\PartnerRepository")
 * @ORM\Table(name="partners")
 */
class Partner implements UserInterface, EquatableInterface
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
	 * @ORM\OneToOne(targetEntity="PartnerVersion", cascade={"persist"})
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
    	$this->actual = new PartnerVersion();
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
    public function getManager()
    {
        return $this->actual->getManager();
    }
    public function setManager(Manager $value, $author = null)
    {
        if ($value != $this->actual->getManager())
            $this->follow($author)->setManager($value);
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
    
    private function follow(User $author = null)
    {
        if ($this->actual->getId() != null)
            $this->actual = $this->actual->follow($author);
        return $this->actual;
    }
}
