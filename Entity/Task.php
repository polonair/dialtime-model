<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\TaskRepository")
 * @ORM\Table(name="tasks")
 */
class Task
{
	const STATE_ACTIVE = "ACTIVE";
	const STATE_CLOSED = "CLOSED";
	
	const REASON_RG = "RG"; // route generated
	const REASON_TO = "TO"; // time out
	const REASON_PO = "PO"; // price out
	const REASON_USER = "USER"; // user have disabled the offer

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;
	/** @ORM\OneToOne(targetEntity="TaskVersion", cascade={"persist"}) */
    private $actual;
    /** @ORM\Column(type="datetime") */
    public $created_at;
	/** @ORM\Column(type="datetime", nullable=true) */
    private $removed_at = null;

    /* constructor */
    public function __construct()
    {
    	$this->actual = new TaskVersion();
    	$this->actual->setEntity($this);
    	$this->created_at = new \DateTime("now");
    }

    /* getters */
    public function getId() { return $this->id; }
	public function getOffer() { return $this->actual->getOffer(); }
	public function getCampaign() { return $this->actual->getCampaign(); }
	public function getMasterPrice() { return $this->actual->getMasterPrice(); }
	public function getPartnerPrice() { return $this->actual->getPartnerPrice(); }
	public function getSystemPrice() { return $this->actual->getSystemPrice(); }
	public function getRate() { return $this->actual->getRate(); }

	/* setters */
	public function setState($state, User $author = null)
	{
		if ($state != $this->actual->getState())
            $this->follow($author)->setState($state);
		return $this;
	}
	public function setCloseReason($reason, User $author = null)
	{
		if ($reason != $this->actual->getCloseReason())
            $this->follow($author)->setCloseReason($reason);
		return $this;
	}
	public function setOffer(Offer $offer, User $author = null)
	{
		if ($offer != $this->actual->getOffer())
            $this->follow($author)->setOffer($offer);
		return $this;
	}
	public function setCampaign(Campaign $campaign, User $author = null)
	{
		if ($campaign != $this->actual->getCampaign())
            $this->follow($author)->setCampaign($campaign);
		return $this;
	}
	public function setMasterPrice($price = 0.0, User $author = null)
	{
		if ($price != $this->actual->getMasterPrice())
            $this->follow($author)->setMasterPrice($price);
		return $this;
	}
	public function setPartnerPrice($price = 0.0, User $author = null)
	{
		if ($price != $this->actual->getPartnerPrice())
            $this->follow($author)->setPartnerPrice($price);
		return $this;
	}
	public function setRate($rate = 0.0, User $author = null)
	{
		if ($rate != $this->actual->getRate())
            $this->follow($author)->setRate($rate);
		return $this;
	}
	public function setSystemPrice($price = 0.0, User $author = null)
	{
		if ($price != $this->actual->getSystemPrice())
            $this->follow($author)->setSystemPrice($price);
		return $this;
	}

	/* follower */
	private function follow(User $author = null)
	{
		if ($this->actual->getId() != null)
			$this->actual = $this->actual->follow($author);
		return $this->actual;
	}
}
