<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="route_rejection_versions")
 */
class RouteRejectionVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Route")
	 */
	private $route;

	/**
	 * @ORM\Column(type="string")
	 */
	private $reason;

	/**
	 * @ORM\Column(type="string")
	 */
	private $state;

	/**
	 * @ORM\ManyToOne(targetEntity="Ticket")
	 */
	private $partner_ticket;

	/**
	 * @ORM\ManyToOne(targetEntity="Ticket")
	 */
	private $master_ticket;

	/**
	 * @ORM\ManyToOne(targetEntity="Transaction")
	 */
	private $transaction;


	/** !required
	 * @ORM\ManyToOne(targetEntity="RouteRejection")
	 */
	private $entity;

	/** !required
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	private $author;

	/** !required
	 * @ORM\Column(type="datetime")
	 */
	private $created_at;

	public function __construct()
	{
		$this->created_at = new \DateTime("now");
	}
    public function setEntity(RouteRejection $value)
    {
    	$this->entity = $value;
    	return $this;
    }

	public function setRoute($value)
	{
		$this->route = $value;
		return $this;
	}
	public function setReason($value)
	{
		$this->reason = $value;
		return $this;
	}
	public function setState($value)
	{
		$this->state = $value;
		return $this;
	}
	public function setPartnerTicket($value)
	{
		$this->partner_ticket = $value;
		return $this;
	}
	public function setMasterTicket($value)
	{
		$this->master_ticket = $value;
		return $this;
	}
	public function setTransaction($value)
	{
		$this->transaction = $value;
		return $this;
	}
	public function getState()
	{
		return $this->state;
	}
	public function getRoute()
	{
		return $this->route;
	}
	public function getReason()
	{
		return $this->reason;
	}
}
