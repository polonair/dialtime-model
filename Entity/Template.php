<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\TemplateRepository")
 * @ORM\Table(name="templates")
 */
class Template
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    private $id;
	/** @ORM\OneToOne(targetEntity="TemplateVersion", cascade={"persist"}) */
    private $actual;
    /** @ORM\Column(type="datetime") */
    private $created_at;
	/** @ORM\Column(type="datetime", nullable=true) */
    private $removed_at = null;

    /* constructor */
    public function __construct()
    {
        $this->actual = (new TemplateVersion())->setEntity($this);
    	$this->created_at = new \DateTime("now");
    }
    
    /* getters */
    public function getId() { return $this->id; }
	public function getName() { return $this->actual->getName(); }
	public function getSource() { return $this->actual->getSource(); }
	public function getCreatedAt() { return $this->actual->getCreatedAt(); }

	/* setters */
	public function setName($name, User $author = null)
	{
		$this->follow($author)->setName($name);
		return $this;
	}
	public function setSource($source, User $author = null)
	{
		$this->follow($author)->setSource($source);
		return $this;
	}

	/* follower */
	private function follow(User $author = null)
	{
		if ($this->actual->getId() !== null)
			$this->actual = $this->actual->follow($author);
		return $this->actual;
	}
}
