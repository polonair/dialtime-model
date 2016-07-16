<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="auths")
 */
class Auth
{
	const TYPE_REGISTRATION = "REGISTRATION";

	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

	/**
	 * @ORM\Column(type="string")
	 */
	private $type;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	private $user;

	/**
	 * @ORM\Column(type="string")
	 */
	private $ip;

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

    public function __construct()
    {
    	$this->created_at = new \DateTime("now");
    }
	public function getType()
	{
		return $this->type;
	}
	public function getUser()
	{
		return $this->user;
	}
	public function getIp()
	{
		return $this->ip;
	}
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}
	public function setUser($user)
	{
		$this->user = $user;
		return $this;
	}
	public function setIp($ip)
	{
		$this->ip = $ip;
		return $this;
	}
} 