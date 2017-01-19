<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\SessionRepository")
 * @ORM\Table(name="sessions")
 */
class Session
{
	/** 
	 * @ORM\Column(type="string")
	 * @ORM\Id
	 */
    private $id;

	/** 
	 * @ORM\Column(type="string")
	 */
    private $realm;
	
	/**
	* @ORM\ManyToOne(targetEntity="User")
	*/
	private $owner;

	/** 
	 * @ORM\Column(type="datetime")
	 */
    private $created_at;

	/** 
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    private $closed_at = null;

    private function generateId($pattern = 'abcdefghijklmnopqrstuvwxyz0123456789', $length = 32)
    {
    	$string = '';
    	$max = strlen($pattern) - 1;
    	for ($i = 0; $i < $length; $i++) $string .= $pattern[mt_rand(0, $max)];
    	return $string;
    }
    public function __construct()
    {
    	$this->id = $this->generateId();
    	$this->created_at = new \DateTime("now");
    	$this->closed_at = (new \DateTime("now"))->add(new \DateInterval("P2D"));
    }
    public function setRealm($realm)
    {
    	$this->realm = $realm;
    	return $this;
    }
    public function setOwner(User $user)
    {
    	$this->owner = $user;
    	return $this;
    }
    public function getId()
    {
    	return $this->id;
    }
    public function getOwner()
    {
    	return $this->owner;
    }
    public function close()
    {
        $this->closed_at = new \DateTime("now");
        return $this;
    }
}
