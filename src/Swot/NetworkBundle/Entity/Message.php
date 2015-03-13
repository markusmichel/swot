<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\MessageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Message
{
    /**
     * @ORM\PrePersist()
     */
    public function updateTimeSent() {
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
     * @var \DateTime
     *
     * @ORM\Column(name="sent", type="datetime")
     */
    private $sent;

    /**
     * @ORM\Column(name="text", type="text")
     * @Assert\NotBlank(message = "entity.message.validation.message.not_blank")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="messages")
     * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
     */
    private $conversation;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_from_id", referencedColumnName="id")
     **/
    private $from;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_to_id", referencedColumnName="id")
     *
     **/
    private $to;

    public function getOtherUser(User $user) {
        return $this->from === $user ? $this->to : $this->from;
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
     * Set sent
     *
     * @param \DateTime $sent
     * @return Message
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
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set conversation
     *
     * @param \Swot\NetworkBundle\Entity\Conversation $conversation
     * @return Message
     */
    public function setConversation(\Swot\NetworkBundle\Entity\Conversation $conversation = null)
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * Get conversation
     *
     * @return \Swot\NetworkBundle\Entity\Conversation 
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * Set from
     *
     * @param \Swot\NetworkBundle\Entity\User $from
     * @return Message
     */
    public function setFrom(\Swot\NetworkBundle\Entity\User $from = null)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return \Swot\NetworkBundle\Entity\User 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param \Swot\NetworkBundle\Entity\User $to
     * @return Message
     */
    public function setTo(\Swot\NetworkBundle\Entity\User $to = null)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return \Swot\NetworkBundle\Entity\User 
     */
    public function getTo()
    {
        return $this->to;
    }
}
