<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\LetterSendingRepository")
 * @ORM\Table(name="letter_sendings")
 */
class LetterSending
{
	const DELIVER_WITH_SMS = "SMS";
	const DELIVER_NONE = "NONE";

	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	private $receiver = null;	
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 */
	private $sender = null;	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Letter")
	 */
	private $letter = null;	
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $send_on = null;	
	
	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	private $type = self::DELIVER_NONE;	
	
	/**
	 *  @ORM\Column(type="datetime", nullable=true)
	 */
	private $sent_on = null;	

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

    public function __construct()
    {
    	$this->created_at = new \DateTime("now");
    }

	public function setReceiver(User $receiver)
	{
		$this->receiver = $receiver;
		return $this;
	}
	public function getReceiver()
	{
		return $this->receiver;
	}
	public function setSender(User $sender = null)
	{
		$this->sender = $sender;
		return $this;
	}
	public function setLetter(Letter $letter)
	{
		$this->letter = $letter;
		return $this;
	}
	public function getLetter()
	{
		return $this->letter;
	}
	public function setSendOn(\DateTime $send_on)
	{
		$this->send_on = $send_on;
		return $this;
	}
	public function setDeliverType($type)
	{
		$this->type = $type;
		return $this;
	}
	public function getDeliverType()
	{
		return $this->type;
	}
	public function setProcessed(\DateTime $sent_on = null)
	{
		$this->sent_on = $sent_on;
		return $this;
	}
} 