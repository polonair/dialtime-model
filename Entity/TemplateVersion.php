<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="template_versions")
 */
class TemplateVersion
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	/** @ORM\Column(type="string") */
    private $name;
    /** @ORM\Column(type="text") */
    private $source;
	/** @ORM\ManyToOne(targetEntity="Template") */
	private $entity;
	/** @ORM\ManyToOne(targetEntity="User") */
	private $author;
	/** @ORM\Column(type="datetime") */
	private $created_at;

	/* constructor */
	public function __construct()
	{
		$this->created_at = new \DateTime("now");
	}

	/* getters */
	public function getId() { return $this->id; }
	public function getName() { return $this->name; }
	public function getSource() { return $this->source; }
	public function getCreatedAt() { return $this->created_at; }

	/* setters */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	public function setSource($source)
	{
		$this->source = $source;
		return $this;
	}
    public function setEntity(Template $entity)
    {
    	$this->entity = $entity;
    	return $this;
    }

	/* follower */
	public function follow(User $author = null)
	{
		$follow = new TemplateVersion();
		$follow->name = $this->name;
		$follow->source = $this->source;
		$follow->entity = $this->entity;
		$follow->author = $author;
		return $follow;
	}
}
