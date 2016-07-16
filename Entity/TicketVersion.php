<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ticket_versions")
 */
class TicketVersion
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	/** @ORM\Column(type="string") */
	private $theme;
	/** @ORM\Column(type="string") */
	private $state = Ticket::STATE_OPEN;
	/** @ORM\ManyToOne(targetEntity="User") */
	private $client;
	/** @ORM\ManyToOne(targetEntity="Admin") */
	private $admin;
	/** @ORM\ManyToOne(targetEntity="Ticket") */
	private $entity;
	/** @ORM\ManyToOne(targetEntity="Ticket") */
	private $author;
	/** @ORM\Column(type="datetime") */
	private $created_at;

	/* constructor */
	public function __construct()
	{
		$this->created_at = new \DateTime("now");
	}

	/* getters */
	public function getId() { return $this->id; }
    public function getTheme() { return $this->theme; }
    public function getState() { return $this->state; }    
	public function getCreatedAt() { return $this->created_at; }

	/* setters */
    public function setEntity(Ticket $value)
    {
    	$this->entity = $value;
    	return $this;
    }
	public function setTheme($value)
	{
		$this->theme = $value;
		return $this;
	}
	public function setClient(User $user = null)
	{
		$this->client = $user;
		return $this;
	}

	/* follower */
	public function follow(User $author = null)
	{
		$follow = new TicketVersion();		
		$follow->theme = $this->theme;
		$follow->state = $this->state;
		$follow->client = $this->client;
		$follow->admin = $this->admin;
		$follow->entity = $this->entity;
		$follow->author = $author;
		return $follow;
	}
}
