<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\DongleDemandingRepository")
 * @ORM\Table(name="dongle_demandings")
 */
class DongleDemanding
{
	const STATE_WAIT = "WAIT";
	const STATE_DECLINED = "DECLINED";
	const STATE_ACCEPTED = "ACCEPTED";

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Ticket")
	 */
	private $ticket;

	/**
	 * @ORM\ManyToOne(targetEntity="Campaign")
	 */
	private $campaign;

	/**
	 * @ORM\Column(type="string")
	 */
	private $state = DongleDemanding::STATE_WAIT;

	/**
	 * @ORM\ManyToOne(targetEntity="Dongle")
	 */
	private $dongle;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    public $resolved_at = null;

    public function __construct()
    {
    }
	public function setTicket(Ticket $value = null)
	{
		$this->ticket = $value;
		return $this;
	}
	public function getCampaign()
	{
		return $this->campaign;
	}
	public function setCampaign(Campaign $value = null)
	{
		$this->campaign = $value;
		return $this;
	}
	public function setDongle(Dongle $dongle = null)
	{
		if ($dongle === null)
		{
			$this->state = DongleDemanding::STATE_DECLINED;
		}
		else
		{
			$this->dongle = $dongle;
			if ($this->campaign->getState() === Campaign::STATE_WAIT) 
				$this->campaign->setState(Campaign::STATE_FORME);
			$this->dongle->setCampaign($this->campaign);
			$this->state = DongleDemanding::STATE_ACCEPTED;
		}
		return $this;
	}
	public function getState()
	{
		return $this->state;
	}
	public function setState($value)
	{
		$this->state = $value;
		return $this;
	}
	public function getTicket()
	{
		return $this->ticket;
	}
}
