<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="intervals")
 */
class Interval
{
	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id; 

    /**
     * @ORM\ManyToOne(targetEntity="Schedule")
     */
    private $schedule;

	/** 
	 * @ORM\Column(type="string")
	 */    
    private $type = "usual";

	/** 
	 * @ORM\Column(type="integer")
	 */  
    private $from_time;

	/** 
	 * @ORM\Column(type="integer")
	 */  
    private $to_time;

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

	/** 
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    public $removed_at = null;

    public function __construct()
    {
    	$this->created_at = new \DateTime("now");
    }
    public function getFrom()
    {
    	return $this->from_time;
    }
    public function getTo()
    {
    	return $this->to_time;
    }
    public function getSchedule()
    {
    	return $this->schedule;
    }
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }
    public function setFrom($from_time)
    {
        $this->from_time = $from_time;
        return $this;
    }
    public function setTo($to_time)
    {
        $this->to_time = $to_time;
        return $this;
    }
    public function isActual()
    {
        $now = ((date("N")-1)*24*60) - (date("H")*60) - date("i");
        return (($now > $this->getFrom()) && ($now > $this->getTo()));
    }
} 