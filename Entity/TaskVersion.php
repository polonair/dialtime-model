<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="task_versions")
 */
class TaskVersion
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	/** @ORM\ManyToOne(targetEntity="Campaign") */
	private $campaign;
	/** @ORM\ManyToOne(targetEntity="Offer") */
	private $offer;
	/** @ORM\Column(type="decimal", precision=11, scale=2) */
	private $master_price;
	/** @ORM\Column(type="decimal", precision=11, scale=2) */
	private $partner_price;	
	/** @ORM\Column(type="decimal", precision=11, scale=2) */
	private $system_price;
	/** @ORM\Column(type="string") */
	private $state = Task::STATE_ACTIVE;
	/** @ORM\Column(type="string", nullable=true) */
	private $close_reason;
	/** @ORM\ManyToOne(targetEntity="Task") */
	private $entity;
	/** @ORM\ManyToOne(targetEntity="User") */
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
	public function getOffer() { return $this->offer; }
	public function getCampaign() { return $this->campaign; }
	public function getMasterPrice() { return $this->master_price; }
	public function getSystemPrice() { return $this->system_price; }
	public function getPartnerPrice() { return $this->partner_price; }
	public function getState() { return $this->state; }
	public function getCloseReason() { return $this->close_reason; }

	/* setters */
	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}
	public function setCloseReason($reason)
	{
		$this->close_reason = $reason;
		return $this;
	}
	public function setOffer(Offer $offer)
	{
		$this->offer = $offer;
		return $this;
	}
	public function setCampaign(Campaign $campaign)
	{
		$this->campaign = $campaign;
		return $this;
	}
	public function setMasterPrice($price)
	{
		$this->master_price = $price;
		return $this;
	}
	public function setPartnerPrice($price)
	{
		$this->partner_price = $price;
		return $this;
	}
	public function setSystemPrice($price)
	{
		$this->system_price = $price;
		return $this;
	}
    public function setEntity(Task $entity)
    {
    	$this->entity = $entity;
    	return $this;
    }

	/* follower */
	public function follow(User $author = null)
	{
		$follow = new TaskVersion();
		$follow->campaign = $this->campaign;
		$follow->offer = $this->offer;
		$follow->master_price = $this->master_price;
		$follow->partner_price = $this->partner_price;	
		$follow->system_price = $this->system_price;
		$follow->state = $this->state;
		$follow->close_reason = $this->close_reason;
		$follow->entity = $this->entity;
		$follow->author = $author;
		return $follow;
	}
}
