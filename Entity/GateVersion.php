<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gate_versions")
 */
class GateVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $host;

    /**
     * @ORM\Column(type="string")
     */
    private $db_user;

    /**
     * @ORM\Column(type="string")
     */
    private $db_password;

    /**
     * @ORM\Column(type="string")
     */
    private $db_name;

    /**
     * @ORM\Column(type="string")
     */
    private $db_port;

    /**
     * @ORM\Column(type="string")
     */
    private $ssh_user;

    /**
     * @ORM\Column(type="string")
     */
    private $ssh_password;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Gate")
	 */
	private $entity;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Gate")
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
    public function setHost($value)
	{
		$this->host = $value;
		return $this;
	}
    public function getHost()
	{
		return $this->host;
	}
	public function setDbUser($value)
	{
		$this->db_user = $value;
		return $this;
	}
	public function getDbUser()
	{
		return $this->db_user;
	}
	public function setDbPassword($value)
	{
		$this->db_password = $value;
		return $this;
	}
	public function getDbPassword()
	{
		return $this->db_password;
	}
	public function getDbName()
	{
		return $this->db_name;
	}
	public function setDbName($value)
	{
		$this->db_name = $value;
		return $this;
	}
	public function setDbPort($value)
	{
		$this->db_port = $value;
		return $this;
	}
	public function getDbPort()
	{
		return $this->db_port;
	}
	public function setSshUser($value)
	{
		$this->ssh_user = $value;
		return $this;
	}
	public function setSshPassword($value)
	{
		$this->ssh_password = $value;
		return $this;
	}
    public function setEntity(Gate $value)
    {
    	$this->entity = $value;
    	return $this;
    }
}
