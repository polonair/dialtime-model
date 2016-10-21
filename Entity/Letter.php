<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="letters")
 */
class Letter
{
	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $title = null;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $body = null;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $priority = 0;	

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

    public function __construct()
    {
    	$this->created_at = new \DateTime("now");
    }


	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	public function setBody($body)
	{
		$this->body = $body;
		return $this;
	}
	public function setPriority($priority)
	{
		$this->priority = $priority;
		return $this;
	}
	public function getBody()
	{
		return $this->body;
	}
} 