<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * ThingStatusUpdate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\ThingStatusUpdateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ThingStatusUpdate
{
    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        $this->setSent(new \DateTime());
    }

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
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent", type="datetime")
     */
    private $sent;

    /**
     * @ORM\ManyToOne(targetEntity="Thing", inversedBy="statusUpdates")
     * @Exclude()
     */
    private $thing;

    /**
     * @VirtualProperty()
     * @return null
     */
    public function getThingId() {
        return $this->thing === null ? null : $this->thing->getId();
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
     * Set message
     *
     * @param string $message
     * @return ThingStatusUpdate
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set sent
     *
     * @param \DateTime $sent
     * @return ThingStatusUpdate
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return \DateTime 
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set thing
     *
     * @param \Swot\NetworkBundle\Entity\Thing $thing
     * @return ThingStatusUpdate
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
}
