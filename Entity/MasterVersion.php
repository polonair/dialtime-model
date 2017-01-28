<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="master_versions")
 */
class MasterVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Master")
	 */
	private $entity;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Manager")
	 */
	private $manager;

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

	public function setEntity(Master $value)
	{
		$this->entity = $value;
		return $this;
	}
	public function getId()
	{
		return $this->id;
	}
	public function getManager()
	{
		return $this->manager;
	}
	public function setManager(Manager $value)
	{
		$this->manager = $value;
		return $this;
	}

	/* follower */
	public function follow(User $author = null)
	{
		$follow = new MasterVersion();
		$follow->manager = $this->manager;
		$follow->entity = $this->entity;
		$follow->author = $author;
		return $follow;
	}
}
