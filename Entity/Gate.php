<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\GateRepository")
 * @ORM\Table(name="gates")
 */
class Gate
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="GateVersion", cascade={"persist"})
	 */
    public $actual;

    /** !required
     * @ORM\Column(type="datetime")
     */
    public $created_at;

	/** !required
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    public $removed_at = null;

    public function __construct()
    {
    	$this->actual = new GateVersion();
    	$this->actual->setEntity($this);
    	$this->created_at = new \DateTime("now");
    }
    public function getId()
    {
        return $this->id;
    }
    public function setHost($value)
	{
		$this->actual->setHost($value);
		return $this;
	}
    public function getHost()
	{
		return $this->actual->getHost();
	}
	public function getDbUser()
	{
		return $this->actual->getDbUser();
	}
	public function getDbPassword()
	{
		return $this->actual->getDbPassword();
	}
	public function getDbName()
	{
		return $this->actual->getDbName();
	}
	public function getDbPort()
	{
		return $this->actual->getDbPort();
	}
	public function setDbUser($value)
	{
		$this->actual->setDbUser($value);
		return $this;
	}
	public function setDbPassword($value)
	{
		$this->actual->setDbPassword($value);
		return $this;
	}
	public function setDbName($value)
	{
		$this->actual->setDbName($value);
		return $this;
	}
	public function setDbPort($value)
	{
		$this->actual->setDbPort($value);
		return $this;
	}
	public function setSshUser($value)
	{
		$this->actual->setSshUser($value);
		return $this;
	}
	public function setSshPassword($value)
	{
		$this->actual->setSshPassword($value);
		return $this;
	}
}
