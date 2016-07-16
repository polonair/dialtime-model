<?php

namespace Polonairs\Dialtime\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="schedule_versions")
 */
class ScheduleVersion
{
	/** !required
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $owner;

    /**
     * @ORM\Column(type="integer")
     */
    private $tz;

	/** !required
	 * @ORM\ManyToOne(targetEntity="Schedule")
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
    public function setEntity(Schedule $value)
    {
    	$this->entity = $value;
    	return $this;
    }
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
        return $this;
    }
    public function setTimezone($timezone)
    {
        $this->tz = $timezone;
        return $this;        
    }
    public function getTimezone()
    {
        return $this->tz;
    }
}
