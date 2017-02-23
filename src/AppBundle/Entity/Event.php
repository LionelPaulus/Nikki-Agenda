<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="creator_id", type="integer")
     */
    private $creatorId;

    /**
     * @var string
     *
     * @ORM\Column(name="google_calendar_id", type="string", length=255)
     */
    private $googleCalendarId;

    /**
     * @var int
     *
     * @ORM\Column(name="team_id", type="integer")
     */
    private $teamId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set creatorId
     *
     * @param integer $creatorId
     *
     * @return Event
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    /**
     * Get creatorId
     *
     * @return int
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * Set googleCalendarId
     *
     * @param string $googleCalendarId
     *
     * @return Event
     */
    public function setGoogleCalendarId($googleCalendarId)
    {
        $this->googleCalendarId = $googleCalendarId;

        return $this;
    }

    /**
     * Get googleCalendarId
     *
     * @return string
     */
    public function getGoogleCalendarId()
    {
        return $this->googleCalendarId;
    }

    /**
     * Set teamId
     *
     * @param integer $teamId
     *
     * @return Event
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;

        return $this;
    }

    /**
     * Get teamId
     *
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }
}

