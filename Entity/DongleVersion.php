<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\DongleRepository")
 * @ORM\Table(name="dongle_versions")
 */
class DongleVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 */
	private $number;

	/**
	 * @ORM\Column(type="string")
	 */
	private $pass_text;

	/**
	 * @ORM\Column(type="string")
	 */
	private $pass_voice;

	/**
	 * @ORM\ManyToOne(targetEntity="Gate")
	 */
	private $gate;

	/** 
	 * @ORM\ManyToOne(targetEntity="Campaign")
	 */
	private $campaign;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Dongle")
	 */
	private $entity;

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
    public function setEntity(Dongle $value)
    {
    	$this->entity = $value;
    	return $this;
    }
    public function setNumber($value)
    {
        $this->number = $value;
        return $this;
    }
    public function getNumber()
    {
        return $this->number;
    }
    public function setPassText($value)
    {
        $this->pass_text = $value;
        return $this;
    }
    public function setPassVoice($value)
    {
        $this->pass_voice = $value;
        return $this;
    }    
    public function getPassVoice()
    {
        return $this->pass_voice;
    }
    public function setGate(Gate $value)
    {
        $this->gate = $value;
        return $this;
    }
    public function getCampaign()
    {
        return $this->campaign;
    }
    public function getGate()
    {
        return $this->gate;
    }
    public function setCampaign(Campaign $value)
    {
    	$this->campaign = $value;
        return $this;
    }
}
