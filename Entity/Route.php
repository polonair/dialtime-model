<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\RouteRepository")
 * @ORM\Table(name="routes")
 */
class Route
{
    const STATE_ORPHAN = "ORPHAN";
    const STATE_ACTIVE = "ACTIVE";
    const STATE_REJECTED = "REJECTED";
    const STATE_SPAM = "SPAM";
    const STATE_REMOVED = "REMOVED";
    const STATE_FORBIDDEN = "FORBIDDEN";

    /** !required
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="RouteVersion", cascade={"persist"})
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
    
    private $attachment;

    public function __construct()
    {
        $this->actual = new RouteVersion();
        $this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function setCustomerNumber($number = null)
    {
        $this->actual->setCustomerNumber($number);
        return $this;
    }
    public function getCustomerNumber()
    {
        return $this->actual->getCustomerNumber();
    }
    public function setMasterPhone(Phone $number = null)
    {
    	$this->actual->setMasterPhone($number);
    	return $this;
    }
    public function setOriginator(Dongle $dongle = null)
    {
    	$this->actual->setOriginator($dongle);
    	return $this;    	
    }    
    public function setTerminator(Dongle $dongle = null)
    {
        $this->actual->setTerminator($dongle);
        return $this;       
    }
    public function getTerminator()
    {
        return $this->actual->getTerminator();
    }
    public function setTask(Task $task = null)
    {
    	$this->actual->setTask($task);
    	return $this;    	
    }   
    public function setState($state)
    {
    	$this->actual->setState($state);
    	return $this;    	
    } 
    public function getState()
    {
        return $this->actual->getState();
    }
    public function setExpiredAt(\DateTime $time)
    {
        $this->actual->setExpiredAt($time);
        return $this;
    }
    public function getExpiredAt()
    {
        return $this->actual->getExpiredAt();
    }
    public function getMasterPhone()
    {
        return $this->actual->getMasterPhone();
    }
    public function getOriginator()
    {
        return $this->actual->getOriginator();
    }
    public function getTask()
    {
        return $this->actual->getTask();
    }
    public function getId()
    {
        return $this->id;
    }
    public function getAttachment()
    {
        return $this->attachment;
    }
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
        return $this;
    }
}
