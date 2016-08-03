<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\ServerJobRepository")
 * @ORM\Table(name="server_jobs")
 */
class ServerJob
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="ServerJobVersion", cascade={"persist"})
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
        $this->actual = new ServerJobVersion();
        $this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function getName()
    {
        return $this->actual->getName();
    }
    public function getOrder()
    {
        return $this->actual->getOrder();
    }

    public function setName($value)
    {
        $this->actual->setName($value);
        return $this;
    }
    public function setOrder($value)
    {
        $this->actual->setOrder($value);
        return $this;
    }
    public function setServer(Server $value = null)
    {
        $this->actual->setServer($value);
        return $this;
    }
}
