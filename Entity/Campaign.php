<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\CampaignRepository")
 * @ORM\Table(name="campaigns")
 */
class Campaign
{
	const STATE_DRAFT = "DRAFT";
	const STATE_WAIT = "WAIT";
	const STATE_FORME = "FORME";
	const STATE_ACTIVE = "ACTIVE";

	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="CampaignVersion", cascade={"persist"})
	 */
    private $actual;

    /** !required
     * @ORM\Column(type="datetime")
     */
    public $created_at;

	/** !required
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    private $removed_at = null;

    public function __construct()
    {
        $this->actual = new CampaignVersion();
        $this->actual->setEntity($this);
    	$this->created_at = new \DateTime("now");
    }
    public function getId()
    {
    	return $this->id;
    }
	public function setOwner($value)
	{
		$this->actual->setOwner($value);
		return $this;
	}
	public function setCategory($value)
	{
		$this->actual->setCategory($value);
		return $this;
	}
	public function setLocation($value)
	{
		$this->actual->setLocation($value);
		return $this;
	}
	public function setState($value)
	{
		if ($this->actual->getId() !== null) $this->actual = $this->actual->follow();
		$this->actual->setState($value);
		return $this;
	}
	public function setBid($value)
	{
		$this->actual->setBid($value);
		return $this;
	}
	public function getState()
	{
		return $this->actual->getState();
	}
	public function getBid()
	{
		return $this->actual->getBid();
	}
	public function getLocation()
	{
		return $this->actual->getLocation();
	}
	public function getCategory()
	{
		return $this->actual->getCategory();
	}
	public function getOwner()
	{
		return $this->actual->getOwner();
	}
	public function getCreatedAt()
	{
		return $this->actual->getCreatedAt();
	}
}
