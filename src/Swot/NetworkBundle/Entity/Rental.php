<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rental
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\RentalRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Rental
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started", type="datetime")
     */
    private $started;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="access_granted_until", type="datetime", nullable=true)
     */
    private $accessGrantedUntil;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="rental_finished", type="datetime", nullable=true)
     */
    private $rentalFinished;

    /**
     * @var string
     *
     * @ORM\Column(name="access_token", type="string", length=255)
     */
    private $accessToken;

    /**
     * @ORM\ManyToOne(targetEntity="Thing", inversedBy="rentals")
     * @ORM\JoinColumn(name="thing_id", referencedColumnName="id")
     */
    private $thing;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="thingsLent")
     * @ORM\JoinColumn(name="user_from_id", referencedColumnName="id")
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="thingsRent")
     * @ORM\JoinColumn(name="user_to_id", referencedColumnName="id")
     */
    private $userTo;

    public function getOtherUser($user) {
        return $this->getUserFrom() === $user ? $this->getUserFrom() : $this->getUserTo();
    }

    public function __construct() {
        $this->accessToken = "";
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        $this->setStarted(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set started
     *
     * @param \DateTime $started
     * @return Rental
     */
    public function setStarted($started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * Get started
     *
     * @return \DateTime 
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * Set accessGrantedUntil
     *
     * @param \DateTime $accessGrantedUntil
     * @return Rental
     */
    public function setAccessGrantedUntil($accessGrantedUntil)
    {
        $this->accessGrantedUntil = $accessGrantedUntil;

        return $this;
    }

    /**
     * Get accessGrantedUntil
     *
     * @return \DateTime 
     */
    public function getAccessGrantedUntil()
    {
        return $this->accessGrantedUntil;
    }

    /**
     * Set rentalFinished
     *
     * @param \DateTime $rentalFinished
     * @return Rental
     */
    public function setRentalFinished($rentalFinished)
    {
        $this->rentalFinished = $rentalFinished;

        return $this;
    }

    /**
     * Get rentalFinished
     *
     * @return \DateTime 
     */
    public function getRentalFinished()
    {
        return $this->rentalFinished;
    }

    /**
     * Set accessToken
     *
     * @param string $accessToken
     * @return Rental
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string 
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set thing
     *
     * @param \Swot\NetworkBundle\Entity\Thing $thing
     * @return Rental
     */
    public function setThing(\Swot\NetworkBundle\Entity\Thing $thing = null)
    {
        $this->thing = $thing;

        return $this;
    }

    /**
     * Get thing
     *
     * @return \Swot\NetworkBundle\Entity\Thing 
     */
    public function getThing()
    {
        return $this->thing;
    }

    /**
     * Set userFrom
     *
     * @param \Swot\NetworkBundle\Entity\User $userFrom
     * @return Rental
     */
    public function setUserFrom(\Swot\NetworkBundle\Entity\User $userFrom = null)
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    /**
     * Get userFrom
     *
     * @return \Swot\NetworkBundle\Entity\User 
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * Set userTo
     *
     * @param \Swot\NetworkBundle\Entity\User $userTo
     * @return Rental
     */
    public function setUserTo(\Swot\NetworkBundle\Entity\User $userTo = null)
    {
        $this->userTo = $userTo;

        return $this;
    }

    /**
     * Get userTo
     *
     * @return \Swot\NetworkBundle\Entity\User 
     */
    public function getUserTo()
    {
        return $this->userTo;
    }
}
