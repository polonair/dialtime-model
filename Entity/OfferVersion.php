<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="offer_versions")
 */
class OfferVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Master")
	 */
	private $owner;

	/**
	 * @ORM\ManyToOne(targetEntity="Phone")
	 */
	private $phone;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Category")
	 */
	private $category;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Location")
	 */
	private $location;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Schedule")
	 */
	private $schedule;
	
	/**
	 * @ORM\Column(type="string")
	 */
	private $state;
	
	/**
	 * @ORM\Column(type="decimal", precision=11, scale=2)
	 */
	private $ask;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Offer")
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
    public function setEntity(Offer $value)
    {
    	$this->entity = $value;
    	return $this;
    }
	public function setOwner(Master $value)
	{
		$this->owner = $value;
		return $this;
	}
	public function getOwner()
	{
		return $this->owner;
	}
	public function setPhone(Phone $value)
	{
		$this->phone = $value;
		return $this;
	}
	public function setCategory(Category $value)
	{
		$this->category = $value;
		return $this;
	}
	public function getCategory()
	{
		return $this->category;
	}
	public function setLocation(Location $value)
	{
		$this->location = $value;
		return $this;
	}
	public function setState($value)
	{
		$this->state = $value;
		return $this;
	}
	public function setAsk($value)
	{
		$this->ask = $value;
		return $this;
	}
    public function getState()
    {
    	return $this->state;
    }
    public function getAsk()
    {
    	return $this->ask;
    }
	public function getLocation()
	{
		return $this->location;
	}
	public function getPhone()
	{
		return $this->phone;
	}
	public function getSchedule()
	{
		return $this->schedule;
	}
	public function setSchedule(Schedule $schedule)
	{
		$this->schedule = $schedule;
		return $this;
	}
}
