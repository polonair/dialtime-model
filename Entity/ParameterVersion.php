<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="parameter_versions")
 */
class ParameterVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/** !required
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @ORM\Column(type="string")
	 */
	private $value;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Parameter")
	 */
	private $entity;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Parameter")
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
    public function setEntity(Parameter $value)
    {
    	$this->entity = $value;
    	return $this;
    }
    public function getName()
    {
    	return $this->name;
    }
    public function getValue()
    {
    	return $this->value;
    }
    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
