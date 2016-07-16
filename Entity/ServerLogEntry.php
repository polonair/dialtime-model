<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="server_log_entries")
 */
class ServerLogEntry
{
	/** 
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;   

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

    public function __construct()
    {
    	$this->created_at = new \DateTime("now");
    }
} 