<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\RouteRejectionRepository")
 * @ORM\Table(name="route_rejections")
 */
class RouteRejection
{
	const STATE_REJECTED_BY_MASTER = "REJECTED_BY_MASTER";
	const STATE_CANCELED_BY_MASTER = "CANCELED_BY_MASTER";
	const STATE_APPROVED_BY_PARTNER = "APPROVED_BY_PARTNER";
	const STATE_DECLINED_BY_PARTNER = "DECLINED_BY_PARTNER";
	const STATE_SOLVED_FOR_MASTER = "SOLVED_FOR_MASTER";
	const STATE_SOLVED_FOR_PARTNER = "SOLVED_FOR_PARTNER";
	const STATE_SOLVED_FOR_BOTH = "SOLVED_FOR_BOTH";
	const STATE_SOLVED_FOR_SYSTEM = "SOLVED_FOR_SYSTEM";

	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="RouteRejectionVersion", cascade={"persist"})
	 */
    public $actual;

    /** !required
     * @ORM\Column(type="datetime")
     */
    public $created_at;

	/** !required
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    public $removed_at = null;

    public function __construct()
    {
        $this->actual = new RouteRejectionVersion();
        $this->actual->setEntity($this);
    	$this->created_at = new \DateTime("now");
    }
	public function setRoute($value)
	{
		$this->actual->setRoute($value);
		return $this;
	}
	public function setReason($value)
	{
		$this->actual->setReason($value);
		return $this;
	}
	public function setState($value)
	{
		$this->actual->setState($value);
		return $this;
	}
	public function getState()
	{
		return $this->actual->getState();
	}
	public function setPartnerTicket($value)
	{
		$this->actual->setPartnerTicket($value);
		return $this;
	}
	public function setMasterTicket($value)
	{
		$this->actual->setMasterTicket($value);
		return $this;
	}
	public function setTransaction($value)
	{
		$this->actual->setTransaction($value);
		return $this;
	}
	public function getRoute()
	{
		return $this->actual->getRoute();
	}
	public function getReason()
	{
		return $this->actual->getReason();
	}
}
