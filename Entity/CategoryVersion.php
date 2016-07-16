<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category_versions")
 */
class CategoryVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @ORM\Column(type="string")
	 */
	private $description;

	/**
	 * @ORM\ManyToOne(targetEntity="Category")
	 */
	private $parent;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Category")
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
    public function setName($value)
    {
    	$this->name = $value;
    	return $this;
    }
    public function getName()
    {
    	return $this->name;
    }
    public function setEntity(Category $value)
    {
    	$this->entity = $value;
    	return $this;
    }
    public function setDescription($value)
    {
    	$this->description = $value;
    	return $this;
    }
    public function setParent($value)
    {
    	$this->parent = $value;
    	return $this;    	
    }
    public function getParent()
    {
        return $this->parent;
    }
}
