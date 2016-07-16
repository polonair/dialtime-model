<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="phone_versions")
 */
class PhoneVersion
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
	private $number;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	private $owner;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $confirmed = false;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $main = false;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Phone")
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
    public function setEntity(Phone $value)
    {
    	$this->entity = $value;
    	return $this;
    }
    public function getNumber()
    {
    	return $this->number;
    }
    public function setNumber($value)
    {
        $this->number = $value;
        return $this;
    }
    public function setOwner($value)
    {
        $this->owner = $value;
        return $this;
    }
    public function getOwner()
    {
        return $this->owner;
    }
    public function getConfirmed()
    {
        return $this->confirmed;
    }
    public function getMain()
    {
        return $this->main;        
    }
    public function setConfirmed($value)
    {
        $this->confirmed = $value;
        return $this;
    }
    public function setMain($value)
    { 
        $this->main = $value;  
        return $this;        
    }
}
