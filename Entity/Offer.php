<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\OfferRepository")
 * @ORM\Table(name="offers")
 */
class Offer
{
	const STATE_OFF = "off";
	const STATE_ON = "on";
	const STATE_AUTO = "auto";
	
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="OfferVersion", cascade={"persist"})
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

    private $task = null;

    public function __construct()
    {
        $this->actual = new OfferVersion();
        $this->actual->setEntity($this);
    	$this->created_at = new \DateTime("now");
    }
    public function getId()
    {
    	return $this->id;
    }
    public function getState()
    {
    	return $this->actual->getState();
    }
    public function getAsk()
    {
    	return $this->actual->getAsk();
    }
	public function setOwner(Master $value)
	{
		$this->actual->setOwner($value);
		return $this;
	}
	public function getOwner()
	{
		return $this->actual->getOwner();
	}
	public function setPhone(Phone $value)
	{
		$this->actual->setPhone($value);
		return $this;
	}
	public function setCategory(Category $value)
	{
		$this->actual->setCategory($value);
		return $this;
	}
	public function getCategory()
	{
		return $this->actual->getCategory();
	}
	public function setLocation(Location $value)
	{
		$this->actual->setLocation($value);
		return $this;
	}
	public function setState($value, $author = null)
	{
		if ($value != $this->actual->getState())
            $this->follow($author)->setState($value);
		return $this;
	}
	public function setAsk($value, $author = null)
	{
		if ($value != $this->actual->getAsk())
            $this->follow($author)->setAsk($value);
		return $this;
	}
	public function getLocation()
	{
		return $this->actual->getLocation();
	}
	public function getPhone()
	{
		return $this->actual->getPhone();
	}
	public function getSchedule()
	{
		return $this->actual->getSchedule();
	}
	public function setSchedule(Schedule $schedule)
	{
		$this->actual->setSchedule($schedule);
		return $this;
	}
	public function isActual()
	{
		return $this->getSchedule()->isActual();
	}
	public function getTask() 
	{
		return $this->task;
	}
	public function setTask(TASk $task = null) 
	{
		$this->task = $task;
		return $this;
	}
	public function isRemoved()
	{
		return ($this->removed_at != null);
	}

	/* follower */
	private function follow(User $author = null)
	{
		if ($this->actual->getId() != null)
			$this->actual = $this->actual->follow($author);
		return $this->actual;
	}
	public function remove()
	{
		$this->removed_at = new \DateTime("now");
	}
}
