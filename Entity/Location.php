<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\LocationRepository")
 * @ORM\Table(name="locations")
 */
class Location
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="LocationVersion", cascade={"persist"})
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
    	$this->actual = new LocationVersion();
    	$this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function getId()
    {
    	return $this->id;
    }
    public function setName($value)
    {
        $this->actual->setName($value);
        return $this;
    }
    public function setLocative($value)
    {
        $this->actual->setLocative($value);
        return $this;
    }
    public function getName()
    {
        return $this->actual->getName();
    }
    public function getLocative()
    {
        return $this->actual->getLocative();
    }
    public function setDescription($value)
    {
    	$this->actual->setDescription($value);
    	return $this;
    }
    public function setParent($value)
    {
    	$this->actual->setParent($value);
    	return $this;    	
    }
    public function getParent()
    {
        return $this->actual->getParent();
    }
}
