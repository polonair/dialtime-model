<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spread_versions")
 */
class SpreadVersion
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	/** @ORM\ManyToOne(targetEntity="Category") */
	private $category;
	/** @ORM\ManyToOne(targetEntity="Location") */
	private $location;
	/** @ORM\Column(type="decimal", precision=11, scale=2) */
	private $value;
	/** @ORM\ManyToOne(targetEntity="Spread") */
	private $entity;
	/** @ORM\ManyToOne(targetEntity="Spread") */
	private $author;
	/** @ORM\Column(type="datetime") */
	private $created_at;

	/* constructor */
	public function __construct()
	{
		$this->created_at = new \DateTime("now");
	}	

	/* getters */
	public function getId()	{ return $this->id; }
    public function getValue() { return $this->value; }
    public function getCategory() { return $this->category; }
    public function getLocation() { return $this->location; }

    /* setters */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    public function setCategory(Category $category = null)
    {
    	$this->category = $category;
    	return $this;
    }
    public function setLocation(Location $location = null)
    {
    	$this->location = $location;
    	return $this;
    }
    public function setEntity(Spread $entity)
    {
    	$this->entity = $entity;
    	return $this;
    }

	/* follower */
	public function follow(User $author = null)
	{
		$follow = new SpreadVersion();
		$follow->category = $this->category;
		$follow->location = $this->location;
		$follow->value = $this->value;
		$follow->entity = $this->entity;
		$follow->author = $author;
		return $follow;
	}
}//101
