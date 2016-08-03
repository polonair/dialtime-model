<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\TicketRepository")
 * @ORM\Table(name="tickets")
 */
class Ticket
{
	const STATE_OPEN = "OPEN";
	const STATE_CLOSE = "CLOSE";
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;
	/** @ORM\OneToOne(targetEntity="TicketVersion", cascade={"persist"}) */
    public $actual;
    /** @ORM\Column(type="datetime") */
    public $created_at;
	/** @ORM\Column(type="datetime", nullable=true) */
    public $removed_at = null;

    /* constructor */
    public function __construct()
    {
    	$this->actual = new TicketVersion();
    	$this->actual->setEntity($this);
    	$this->created_at = new \DateTime("now");
    }

    /* getters */
    public function getId() { return $this->id; }
    public function getTheme() { return $this->actual->getTheme(); }
    public function getState() { return $this->actual->getState(); }
	public function getCreatedAt() { return $this->created_at; }

    /* setters */
	public function setTheme($value, User $author = null)
	{
		$this->follow($author)->setTheme($value);
		return $this;
	}
	public function setClient(User $client = null, User $author = null)
	{
		$this->follow($author)->setClient($client);
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
