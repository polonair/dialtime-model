<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="server_job_versions")
 */
class ServerJobVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/** !required
	 * @ORM\ManyToOne(targetEntity="ServerJob")
	 */
	private $entity;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Server")
	 */
	private $server;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	private $position;

	/**
	 * @ORM\Column(type="string")
	 */
	private $name;

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
    public function setEntity(ServerJob $value)
    {
    	$this->entity = $value;
    	return $this;
    }
    public function getName()
    {
    	return $this->name;
    }
    public function getOrder()
    {
        return $this->position;
    }

    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }
    public function setOrder($value)
    {
        $this->position = $value;
        return $this;
    }
    public function setServer(Server $value = null)
    {
        $this->server = $value;
        return $this;
    }
}
