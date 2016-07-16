<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\SpreadRepository")
 * @ORM\Table(name="spreads")
 */
class Spread
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;
	/** @ORM\OneToOne(targetEntity="SpreadVersion", cascade={"persist"}) */
    private $actual;
    /** @ORM\Column(type="datetime") */
    private $created_at;
    /** @ORM\Column(type="datetime", nullable=true) */
    private $removed_at = null;

    /* constructor */
    public function __construct()
    {
    	$this->actual = (new SpreadVersion())->setEntity($this);
        $this->created_at = new \DateTime("now");
    }

    /* getters */    
    public function getCategory() { return $this->actual->getCategory(); }
    public function getLocation() { return $this->actual->getLocation(); }

    /* setters */
    public function setValue($value, User $author = null)
    {
        if ($value != $this->actual->getValue())
            $this->follow($author)->setValue($value);
        return $this;
    }
    public function setCategory(Category $category = null, User $author = null)
    {
        if ($category != $this->actual->getCategory())
            $this->follow($author)->setCategory($category);
        return $this;
    }
    public function setLocation(Location $location = null, User $author = null)
    {
        if ($location != $this->actual->getLocation())
            $this->follow($author)->setLocation($location);
        return $this;
    }

    /* follower */
    private function follow(User $author = null)
    {
        if ($this->actual->getId() != null)
            $this->actual = $this->actual->follow($author);
        return $this->actual;
    }
}//66
