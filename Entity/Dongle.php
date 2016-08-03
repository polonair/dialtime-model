<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Polonairs\Dialtime\ModelBundle\Repository\DongleRepository")
 * @ORM\Table(name="dongles")
 */
class Dongle
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
    public $id;

	/** !required
	 * @ORM\OneToOne(targetEntity="DongleVersion", cascade={"persist"})
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
        $this->actual = new DongleVersion();
        $this->actual->setEntity($this);
        $this->created_at = new \DateTime("now");
    }
    public function getId()
    {
        return $this->id;
    }
    public function setNumber($value)
    {
        $this->actual->setNumber($value);
        return $this;
    }
    public function getNumber()
    {
        return $this->actual->getNumber();
    }
    public function setPassText($value)
    {
        $this->actual->setPassText($value);
        return $this;
    }
    public function setPassVoice($value)
    {
        $this->actual->setPassVoice($value);
        return $this;
    }
    public function setGate(Gate $value)
    {
        $this->actual->setGate($value);
        return $this;
    }
    public function getCampaign()
    {
        return $this->actual->getCampaign();
    }
    public function getPassVoice()
    {
        return $this->actual->getPassVoice();
    }
    public function getGate()
    {
        return $this->actual->getGate();
    }
    public function setCampaign(Campaign $value)
    {
        $this->actual->setCampaign($value);
        return $this;
    }

    public static function equals(Dongle $a, Dongle $b)
    {
        return ($a->id === $b->id);
    }
}
