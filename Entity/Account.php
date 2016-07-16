<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="accounts")
 */
class Account
{
	const STATE_ACTIVE = "ACTIVE";
	
	const CURRENCY_RUR = "RUR";

	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

	/**
	* @ORM\Column(type="decimal", precision=11, scale=2)
	*/
	private $balance = 0;

	/**
	* @ORM\Column(type="decimal", precision=11, scale=2)
	*/
	private $credit = 0;

	/**
	* @ORM\Column(type="decimal", precision=11, scale=2)
	*/
	private $income_hold = 0;

	/**
	* @ORM\Column(type="decimal", precision=11, scale=2)
	*/
	private $outcome_hold = 0;
	
	/**
	* @ORM\Column(type="string")
	*/
	private $currency;
	
	/**
	* @ORM\ManyToOne(targetEntity="User")
	*/
	private $owner;
	
	/**
	* @ORM\Column(type="string")
	*/
	private $state;
	
	/**
  	* @ORM\Column(type="string", nullable=true)
  	*/
	private $name;      

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

	/** 
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    public $closed_at = null;

    public function __construct()
    {
    	$this->created_at = new \DateTime("now");
    }

	public function setBalance($value)
	{
		$this->balance = $value;
		return $this;
	}
	public function getBalance()
	{
		return $this->balance;
	}
	public function setCurrency($value)
	{
		$this->currency = $value;
		return $this;
	}
	public function setOwner($value)
	{
		$this->owner = $value;
		return $this;
	}
	public function setState($value)
	{
		$this->state = $value;
		return $this;
	}
	public function setName($value)
	{
		$this->name = $value;
		return $this;
	}
	public function decreaseBalance($value)
	{
		$this->balance-=$value;
	}
	public function increaseBalance($value)
	{
		$this->balance+=$value;
	}
	public function setOutcomeHold($value)
	{
		$this->outcome_hold = $value;
		return $this;
	}
	public function getOutcomeHold()
	{
		return $this->outcome_hold;
	}
	public function setIncomeHold($value)
	{
		$this->income_hold = $value;
		return $this;
	}
	public function getIncomeHold()
	{
		return $this->income_hold;
	}
	public function getId()
	{
		return $this->id;
	}
	public function getOwner()
	{
		return $this->owner;
	}
	public function getname()
	{
		return $this->name;
	}
	public function getCurrency()
	{
		return $this->currency;
	}
	public function getState()
	{
		return $this->state;
	}
	public function getCreatedAt()
	{
		return $this->created_at;
	}
	public function getClosedAt()
	{
		return $this->closed_at;
	}
	public function getCredit()
	{
		return $this->credit;
	}
	public function setCredit($value)
	{
		$this->credit = $value;
		return $this;
	}
} 