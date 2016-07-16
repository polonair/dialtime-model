<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="servers")
 */
class Server
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="ServerVersion", cascade={"persist"})
	 */
    private $actual;

    /** !required
     * @ORM\Column(type="datetime")
     */
    public $created_at;

    /** !required
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $removed_at = null;

    public function __construct()
    {
        $this->actual = new ServerVersion();
        $this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function getId()
    {
    	return $this->id;
    }
}
