<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="campaign_versions")
 */
class CampaignVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Partner")
	 */
	private $owner;

	/**
	 * @ORM\ManyToOne(targetEntity="Category")
	 */
	private $category;

	/**
	 * @ORM\ManyToOne(targetEntity="Location")
	 */
	private $location;

	/**
	 * @ORM\Column(type="string")
	 */
	private $state;

	/**
	 * @ORM\Column(type="decimal", precision=11, scale=2)
	 */
	private $bid;	


	/** !required
	 * @ORM\ManyToOne(targetEntity="Campaign")
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
	public function getId()
	{
		return $this->id;
	}
    public function setEntity(Campaign $value)
    {
    	$this->entity = $value;
    	return $this;
    }
	public function setOwner($value)
	{
		$this->owner = $value;
		return $this;
	}
	public function setCategory($value)
	{
		$this->category = $value;
		return $this;
	}
	public function setLocation($value)
	{
		$this->location = $value;
		return $this;
	}
	public function setState($value)
	{
		$this->state = $value;
		return $this;
	}
	public function setBid($value)
	{
		$this->bid = $value;
		return $this;
	}
	public function getState()
	{
		return $this->state;
	}
	public function getBid()
	{
		return $this->bid;
	}
	public function getLocation()
	{
		return $this->location;
	}
	public function getCategory()
	{
		return $this->category;
	}
	public function follow()
	{
		$follow = new CampaignVersion();

		$follow->owner = $this->owner;
		$follow->category = $this->category;
		$follow->location = $this->location;
		$follow->state = $this->state;
		$follow->bid = $this->bid;	
		$follow->entity = $this->entity;

		return $follow;
	}
	public function getOwner()
	{
		return $this->owner;
	}
	public function getCreatedAt()
	{
		return $this->created_at;
	}
}
