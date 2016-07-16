<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="route_versions")
 */
class RouteVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $customer_number;

	/**
	 * @ORM\ManyToOne(targetEntity="Phone")
	 */
	private $master_phone;

	/**
	 * @ORM\ManyToOne(targetEntity="Dongle")
	 */
	private $originator;

	/**
	 * @ORM\ManyToOne(targetEntity="Dongle")
	 */
	private $terminator;

	/**
	 * @ORM\ManyToOne(targetEntity="Task")
	 */
	private $task;

	/**
	 * @ORM\Column(type="string")
	 */
	private $state;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $expired_at;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Route")
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
    public function setEntity(Route $value)
    {
    	$this->entity = $value;
    	return $this;
    }
    public function setCustomerNumber($number)
    {
    	$this->customer_number = $number;
    	return $this;
    }
    public function setMasterPhone(Phone $number = null)
    {
    	$this->master_phone = $number;
    	return $this;
    }
    public function setOriginator(Dongle $dongle = null)
    {
    	$this->originator = $dongle;
    	return $this;    	
    }
    public function setTerminator(Dongle $dongle = null)
    {
    	$this->terminator = $dongle;
    	return $this;    	
    }
    public function getTerminator()
    {
        return $this->terminator;
    }
    public function setTask(Task $task = null)
    {
    	$this->task =$task;
    	return $this;    	
    }    
    public function setState($state)
    {
    	$this->state = $state;
    	return $this;    	
    } 
    public function setExpiredAt(\DateTime $time)
    {
    	$this->expired_at = $time;
    	return $this;
    }
    public function getMasterPhone()
    {
        return $this->master_phone;
    }
    public function getOriginator()
    {
        return $this->originator;
    }
    public function getTask()
    {
        return $this->task;
    }
    public function getCustomerNumber()
    {
        return $this->customer_number;
    }
    public function getExpiredAt()
    {
        return $this->expired_at;
    }
    public function getState()
    {
        return $this->state;
    }
}