<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;
	/** @ORM\OneToOne(targetEntity="UserVersion", cascade={"persist"}) */
    private $actual;
	/** @ORM\Column(type="datetime", nullable=true) */
    private $removed_at = null;
    /** @ORM\Column(type="datetime") */
    private $created_at;

    /* constructor */
    public function __construct()
    {
    	$this->actual = (new UserVersion())->setEntity($this);
    	$this->created_at = new \DateTime("now");
    }

    /* getters */
    public function getId() { return $this->id; }
	public function getUsername() { return $this->actual->getUsername(); }
	public function getPassword() { return $this->actual->getPassword(); }
	public function getMainAccount() { return $this->actual->getMainAccount(); }

	/* setters */
	public function setUsername($username, User $author = null)
	{
		$this->follow($author)->setUsername($username);
		return $this;
	}
	public function setPassword($password, User $author = null)
	{
		$this->follow($author)->setPassword($password);
		return $this;
	}
	public function setMainAccount(Account $value = null, User $author = null)
	{
		$this->follow($author)->setMainAccount($value);
		return $this;
	}
	public function setMainSchedule(Schedule $schedule = null, User $author = null)
	{
		$this->follow($author)->setMainSchedule($schedule);
		return $this;
	}

	/* follower */
	private function follow(User $author = null)
	{
		if ($this->actual->getId() !== null)
			$this->actual = $this->actual->follow($author);
		return $this->actual;
	}
}
