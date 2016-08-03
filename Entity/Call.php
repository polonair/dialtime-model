<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\CallRepository")
 * @ORM\Table(name="calls")
 */
class Call
{
	const DIRECTION_RG="RG";
	const DIRECTION_MO="MO";
	const DIRECTION_MT="MT";
	const DIRECTION_RRG="RRG";

	const RESULT_ANSWER="ANSWER";
	const RESULT_CANCEL="CANCEL";
	const RESULT_BUSY="BUSY";

	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

	/**
	* @ORM\ManyToOne(targetEntity="Route")
	*/
	private $route;

	/** 
	 * @ORM\Column(type="datetime")
	 */
	private $created_at;

	/**
	* @ORM\Column(type="string")
	*/
	private $direction;

	/**
	* @ORM\Column(type="integer")
	*/
	private $dial_length;

	/**
	* @ORM\Column(type="integer")
	*/
	private $answer_length;
	
	/**
	* @ORM\Column(type="string")
	*/
	private $result;
	
	/**
	* @ORM\Column(type="blob", nullable=true)
	*/
	private $record;

	/** 
	 * @ORM\ManyToOne(targetEntity="Transaction")
	 */
	private $transaction;

	public function getId()
	{
		return $this->id;
	}
	public function setRoute(Route $route)
	{
		$this->route = $route;
		return $this;
	}
	public function getRoute()
	{
		return $this->route;
	}
	public function setDirection($direction)
	{
		$this->direction = $direction;
		return $this;
	}
	public function setDialLength($value)
	{
		$this->dial_length = $value;
		return $this;
	}
	public function setAnswerLength($value)
	{
		$this->answer_length = $value;
		return $this;
	}
	public function getAnswerLength()
	{
		return $this->answer_length;
	}
	public function setResult($value)
	{
		$this->result = $value;
		return $this;
	}
	public function setRecord($value = null)
	{
		$this->record = $value;
		return $this;
	}
	public function setCreatedAt(\DateTime $time)
    {
    	$this->created_at = $time;
    	return $this;
    }
	public function getCreatedAt()
    {
    	return $this->created_at;
    }
    public function setTransaction(Transaction $transaction = null)
    {
    	$this->transaction = $transaction;
    	return $this;
    }
	public function getDirection()
	{
		return $this->direction;
	}
	public function getRecord()
	{
		return $this->record;
	}
	public function getTransaction()
	{
		return $this->transaction;
	}
}