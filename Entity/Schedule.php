<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\ScheduleRepository")
 * @ORM\Table(name="schedules")
 */
class Schedule
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

    private $intervals;

	/** !required
	 * @ORM\OneToOne(targetEntity="ScheduleVersion", cascade={"persist"})
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
    	$this->intervals = new ArrayCollection();
    	$this->actual = new ScheduleVersion();
    	$this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function addInterval(Interval $interval)
    {
    	if ($this->intervals === null) $this->intervals = new ArrayCollection();
    	$this->intervals->add($interval);
    	return $this;
    }
    public function getId()
    {
    	return $this->id;
    }
    public function getIntervals()
    {
    	if ($this->intervals === null) $this->intervals = new ArrayCollection();
    	return $this->intervals;
    }
    public function setOwner(User $owner)
    {
        $this->actual->setOwner($owner);
        return $this;
    }
    public function setTimezone($timezone)
    {
        $this->actual->setTimezone($timezone);
        return $this;        
    }
    public function getTimezone()
    {
        return $this->actual->getTimezone();
    }
    public function isActual()
    {
        $intervals = $this->getIntervals();
        dump($intervals);
        if (count($intervals) < 1) return false;

        foreach($intervals as $i)
        {
            if ($i->isActual()) return true;
        }
        return false;
    }
}
