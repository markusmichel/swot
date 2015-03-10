<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thing
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\ThingRepository")
 */
class Thing
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="owner_since", type="datetime")
     */
    private $ownerSince;

    /**
     * @var string
     *
     * @ORM\Column(name="access_token", type="string", length=255)
     */
    private $accessToken;

    /**
     * @ORM\OneToOne(targetEntity="Ownership", mappedBy="thing", cascade={"persist"})
     */
    private $ownership;

    /**
     * @ORM\OneToMany(targetEntity="Rental", mappedBy="thing")
     */
    private $rentals;

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
     * Set name
     *
     * @param string $name
     * @return Thing
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ownerSince.
     * Should not be called.
     * Model automatically sets ownerSince when owner changes.
     * @todo: check if it can safely be removed.
     * @see setOwner
     *
     * @param \DateTime $ownerSince
     * @return Thing
     */
    public function setOwnerSince($ownerSince)
    {
        $this->ownerSince = $ownerSince;

        return $this;
    }

    /**
     * Get ownerSince
     *
     * @return \DateTime 
     */
    public function getOwnerSince()
    {
        return $this->ownerSince;
    }

    /**
     * Set accessToken
     *
     * @param string $accessToken
     * @return Thing
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
     * Set owner.
     * Automatically sets owner since when owner changes.
     *
     * @param \Swot\NetworkBundle\Entity\Ownership $ownership
     * @return Thing
     */
    public function setOwnership(\Swot\NetworkBundle\Entity\Ownership $ownership = null)
    {
        if($this->getOwnership() !== $ownership) $this->setOwnerSince(new \DateTime());
        $this->ownership = $ownership;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Swot\NetworkBundle\Entity\Ownership 
     */
    public function getOwnership()
    {
        return $this->ownership;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rentals = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add rentals
     *
     * @param \Swot\NetworkBundle\Entity\Rental $rentals
     * @return Thing
     */
    public function addRental(\Swot\NetworkBundle\Entity\Rental $rentals)
    {
        $this->rentals[] = $rentals;

        return $this;
    }

    /**
     * Remove rentals
     *
     * @param \Swot\NetworkBundle\Entity\Rental $rentals
     */
    public function removeRental(\Swot\NetworkBundle\Entity\Rental $rentals)
    {
        $this->rentals->removeElement($rentals);
    }

    /**
     * Get rentals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRentals()
    {
        return $this->rentals;
    }
}
