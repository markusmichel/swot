<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ownership
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\OwnershipRepository")
 */
class Ownership
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
     * @ORM\Column(name="since", type="datetime")
     */
    private $since;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownerships")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\OneToOne(targetEntity="Thing", inversedBy="owner")
     * @ORM\JoinColumn(name="thing_id", referencedColumnName="id")
     */
    private $thing;


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
     * Set since.
     * Should not be called.
     * Model automatically sets since when owner or thing changes.
     *
     * @param \DateTime $since
     * @return Ownership
     */
    public function setSince($since)
    {
        $this->since = $since;

        return $this;
    }

    /**
     * Get since
     *
     * @return \DateTime 
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * Set owner
     *
     * @param \Swot\NetworkBundle\Entity\User $owner
     * @return Ownership
     */
    public function setOwner(\Swot\NetworkBundle\Entity\User $owner = null)
    {
        if($this->owner !== $owner) $this->setSince(new \DateTime());
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Swot\NetworkBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set thing
     *
     * @param \Swot\NetworkBundle\Entity\Thing $thing
     * @return Ownership
     */
    public function setThing(\Swot\NetworkBundle\Entity\Thing $thing = null)
    {
        if($this->thing !== $thing) $this->setSince(new \DateTime());
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
}
