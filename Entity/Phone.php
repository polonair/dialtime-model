<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\PhoneRepository")
 * @ORM\Table(name="phones")
 */
class Phone
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="PhoneVersion", cascade={"persist"})
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
        $this->actual = new PhoneVersion();
        $this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function getId()
    {
        return $this->id;
    }
    public function getNumber()
    {
    	return $this->actual->getNumber();
    }
    public function setNumber($value)
    {
        $this->actual->setNumber($value);
        return $this;
    }
    public function setOwner($value)
    {
        $this->actual->setOwner($value);
        return $this;
    }
    public function getOwner()
    {
        return $this->actual->getOwner();
    }
    public function getConfirmed()
    {
        return $this->actual->getConfirmed();
    }
    public function getMain()
    {
        return $this->actual->getMain();        
    }
    public function setConfirmed($value)
    {
        $this->actual->setConfirmed($value);
        return $this;
    }
    public function setMain($value)
    { 
        $this->actual->setMain($value);
        return $this;        
    }
}
