<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Polonairs\Dialtime\ModelBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_versions")
 */
class UserVersion
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	/** @ORM\Column(type="string") */
	private $username;
	/** @ORM\Column(type="string") */
	private $password;
	/** @ORM\Column(type="string") */
	private $state;
	/** @ORM\ManyToOne(targetEntity="Account") */
	private $main_account;
	/** @ORM\ManyToOne(targetEntity="Account") */
	private $rate_account;
	/** @ORM\ManyToOne(targetEntity="Schedule") */
	private $main_schedule;
	/** @ORM\ManyToOne(targetEntity="User") */
	private $entity;
	/** @ORM\ManyToOne(targetEntity="User") */
	private $author;
	/** @ORM\Column(type="datetime") */
	private $created_at;

	/* constructor */
	public function __construct()
	{
		$this->created_at = new \DateTime("now");
		$this->state = User::STATE_JUST_REGISTERED;
	}

	/* getters */
	public function getId()	{ return $this->id;	}
	public function getUsername() { return $this->username;	}
	public function getPassword() { return $this->password;	}
	public function getMainAccount() { return $this->main_account;	}
	public function getRateAccount() { return $this->rate_account;	}
	public function getState() { return $this->state;	}

	/* setters */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}
	public function setMainAccount(Account $account = null)
	{
		$this->main_account = $account;
		return $this;
	}
	public function setRateAccount(Account $account = null)
	{
		$this->rate_account = $account;
		return $this;
	}
	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}
	public function setMainSchedule(Schedule $schedule = null)
	{
		$this->main_schedule = $schedule;
		return $this;
	}
	public function setEntity(User $entity)
	{
		$this->entity = $entity;
		return $this;
	}

	/* follower */
	public function follow(User $author = null)
	{
		$follow = new UserVersion();
		$follow->username = $this->username;
		$follow->password = $this->password;
		$follow->main_account = $this->main_account;
		$follow->rate_account = $this->rate_account;
		$follow->main_schedule = $this->main_schedule;
		$follow->entity = $this->entity;
		$follow->state = $this->state;
		$follow->author = $author;
		return $follow;
	}
}
