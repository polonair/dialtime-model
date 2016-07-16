<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\TransactionRepository")
 * @ORM\Table(name="transactions")
 */
class Transaction
{
    const STATE_OPEN   = "OPEN";
    const STATE_HOLD   = "HOLD";
    const STATE_CANCEL = "CANCEL";
    const STATE_CLOSE  = "CLOSE";
    const STATE_BROKEN = "BROKEN";

    const EVENT_TRADE = "TRADE";
    const EVENT_FILLUP = "FILLUP";

	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;    

    /** 
     * @ORM\Column(type="string")
     */
    public $event;

    /** 
     * @ORM\Column(type="datetime")
     */
    public $open_at;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $hold_at = null;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $cancel_at = null;

	/** 
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    public $close_at = null;

    public function __construct()
    {
    	$this->entries = new ArrayCollection();
    	$this->open_at = new \DateTime("now");
    }
    public function getId()
    {
    	return $this->id;
    }
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }
    public function isOpen()
    {
        return (
            $this->open_at   !== null &&
            $this->hold_at   === null &&
            $this->cancel_at === null &&
            $this->close_at  === null    );
    }
    public function isHold()
    {
        return (
            $this->open_at   !== null &&
            $this->hold_at   !== null &&
            $this->cancel_at === null &&
            $this->close_at  === null    );
    }
    public function hold()
    {
        if ($this->isOpen())
        {
            $this->hold_at = new \DateTime("now");
        }
        return $this;
    }
    public function close()
    {
        if ($this->isHold())
        {
            $this->close_at = new \DateTime("now");
        }
        return $this;        
    }
    public function cancel()
    {
        if ($this->isHold())
        {
            $this->cancel_at = new \DateTime("now");
        }
        return $this;        
    }
    public function getOpenAt()
    {
        return $this->open_at;
    }
    public function getStatus()
    {
        if ($this->open_at   !== null &&
            $this->hold_at   === null &&
            $this->cancel_at === null &&
            $this->close_at  === null)
            return self::STATE_OPEN;
        if ($this->open_at   !== null &&
            $this->hold_at   !== null &&
            $this->cancel_at === null &&
            $this->close_at  === null)
            return self::STATE_HOLD;
        if ($this->open_at   !== null &&
            $this->hold_at   !== null &&
            $this->cancel_at !== null &&
            $this->close_at  === null)
            return self::STATE_CANCEL;
        if ($this->open_at   !== null &&
            $this->hold_at   !== null &&
            $this->cancel_at === null &&
            $this->close_at  !== null)
            return self::STATE_CLOSE;
        return self::STATE_BROKEN;
    }
} 