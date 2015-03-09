<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Friendship
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\FriendshipRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Friendship
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_who_id", referencedColumnName="id")
     */
    private $userWho;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_with_id", referencedColumnName="id")
     */
    private $userWith;

    /**
     * Helper function to return the user who is NOT the passed other.
     * @param User $user User to compare
     * @return \Swot\NetworkBundle\Entity\User The other user of the relation in the friendship.
     */
    public function getOtherUser(User $user) {
        return ($user === $this->userWho) ? $this->userWith : $this->userWho;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        $this->setSince(new \DateTime());
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
     * Set since
     *
     * @param \DateTime $since
     * @return Friendship
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
     * Set userWho
     *
     * @param \Swot\NetworkBundle\Entity\User $userWho
     * @return Friendship
     */
    public function setUserWho(\Swot\NetworkBundle\Entity\User $userWho = null)
    {
        $this->userWho = $userWho;

        return $this;
    }

    /**
     * Get userWho
     *
     * @return \Swot\NetworkBundle\Entity\User 
     */
    public function getUserWho()
    {
        return $this->userWho;
    }

    /**
     * Set userWith
     *
     * @param \Swot\NetworkBundle\Entity\User $userWith
     * @return Friendship
     */
    public function setUserWith(\Swot\NetworkBundle\Entity\User $userWith = null)
    {
        $this->userWith = $userWith;

        return $this;
    }

    /**
     * Get userWith
     *
     * @return \Swot\NetworkBundle\Entity\User 
     */
    public function getUserWith()
    {
        return $this->userWith;
    }
}
