<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\ParameterRepository")
 * @ORM\Table(name="parameters")
 */
class Parameter
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="ParameterVersion", cascade={"persist"})
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
    	$this->actual = new ParameterVersion();
    	$this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function getName()
    {
        return $this->actual->getName();
    }
    public function getValue()
    {
        return $this->actual->getValue();
    }
    public function setName($value)
    {
        $this->actual->setName($value);
        return $this;
    }
    public function setValue($value)
    {
        $this->actual->setValue($value);
        return $this;
    }
}
