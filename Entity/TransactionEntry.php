<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\TransactionEntryRepository")
 * @ORM\Table(name="transaction_entries")
 */
class TransactionEntry
{
	const ROLE_SELLER = "SELLER";
	const ROLE_BUYER = "BUYER";

	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Transaction")
	 */
	private $transaction;

	/**
	 * @ORM\ManyToOne(targetEntity="Account")
	 */
	private $acc_from;

	/**
	 * @ORM\ManyToOne(targetEntity="Account")
	 */
	private $acc_to;

	/**
	 * @ORM\Column(type="decimal", precision=11, scale=2)
	 */
	private $amount;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $role;

    public function __construct(){ }
    public function getId()
    {
    	return $this->id;
    }
	public function setTransaction(Transaction $value)
	{
		$this->transaction = $value;
		return $this;
	}
	public function setFrom(Account $value)
	{
		$this->acc_from = $value;
		return $this;
	}
	public function setTo(Account $value)
	{
		$this->acc_to = $value;
		return $this;
	}
	public function getFrom()
	{
		return $this->acc_from;
	}
	public function getTo()
	{
		return $this->acc_to;
	}
	public function setAmount($value)
	{
		$this->amount = $value;
		return $this;
	}
	public function getAmount()
	{
		return $this->amount;
	}
	public function setCurrency($value)
	{
		$this->currency = $value;
		return $this;
	}
	public function getTransaction()
	{
		return $this->transaction;
	}
	public function setRole($role)
	{
		$this->role = $role;
		return $this;
	}
	public function getRole()
	{
		return $this->role;
	}
} 