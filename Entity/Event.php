<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\EventRepository")
 * @ORM\Table(name="events")
 */
class Event
{
	const EVENT_TYPE_CREATION = "CREATION";

	const EVENT_CLASS_ROUTE = "ROUTE";

	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $event_type = null;    	

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $class = null;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $object = null;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $version = null;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $processed = null;

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

    public function __construct()
    {
    	$this->created_at = new \DateTime("now");
    }
	public function setType($event_type)
	{
		$this->event_type = $event_type;
		return $this;
	}
	public function setClass($class)
	{
		$this->class = $class;
		return $this;
	}
	public function setObject($object)
	{
		$this->object = $object;
		return $this;
	}
	public function setVersion($version)
	{
		$this->version = $version;
		return $this;
	}
	public function setProcessed($processed)
	{
		$this->processed = $processed;
		return true;
	}

	public function getType()
	{
		return $this->event_type;
	}
	public function getClass()
	{
		return $this->class;
	}
	public function getObject()
	{
		return $this->object;
	}
	public function getVersion()
	{
		return $this->version;
	}
} 