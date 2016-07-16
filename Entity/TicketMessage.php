<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ticket_messages")
 */
class TicketMessage
{
	const DIRECTION_FROM_CLIENT = "FROM_CLIENT";
	const DIRECTION_FROM_ADMIN = "FROM_ADMIN";

	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Ticket")
	 */
	private $ticket;

	/**
	 * @ORM\Column(type="text")
	 */
	private $text;

	/**
	 * @ORM\Column(type="string")
	 */
	private $direction;
	
	/**
  	* @ORM\Column(type="boolean")
  	*/
	private $removed = false;      

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

    public function __construct()
    {
    	$this->created_at = new \DateTime("now");
    }
	public function setDirection($direction)
	{
		$this->direction = $direction;
		return $this;
	}
	public function setTicket(Ticket $ticket)
	{
		$this->ticket = $ticket;
		return $this;
	}
	public function setMessage($text)
	{
		$this->text = $text;
		return $this;
	}
} 