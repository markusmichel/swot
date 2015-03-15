<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conversation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\ConversationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Conversation
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
     * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation")
     */
    private $messages;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="conversations")
     */
    private $involvedUsers;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateUpdatedValue() {
        $this->updated = new \DateTime();
    }

    public function getAllUsersBut(User $user) {
        return $this->getInvolvedUsers()->filter(function($entry) use ($user) {
            return $entry->getId() !== $user->getId();
        });
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
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add messages
     *
     * @param \Swot\NetworkBundle\Entity\Message $messages
     * @return Conversation
     */
    public function addMessage(\Swot\NetworkBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Swot\NetworkBundle\Entity\Message $messages
     */
    public function removeMessage(\Swot\NetworkBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Conversation
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add involvedUsers
     *
     * @param \Swot\NetworkBundle\Entity\User $involvedUsers
     * @return Conversation
     */
    public function addInvolvedUser(\Swot\NetworkBundle\Entity\User $involvedUsers)
    {
        $this->involvedUsers[] = $involvedUsers;

        return $this;
    }

    /**
     * Remove involvedUsers
     *
     * @param \Swot\NetworkBundle\Entity\User $involvedUsers
     */
    public function removeInvolvedUser(\Swot\NetworkBundle\Entity\User $involvedUsers)
    {
        $this->involvedUsers->removeElement($involvedUsers);
    }

    /**
     * Get involvedUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvolvedUsers()
    {
        return $this->involvedUsers;
    }
}
