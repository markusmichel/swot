<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface, \Serializable
{

    const ACCESS_TYPE_PUBLIC        = 'public';
    const ACCESS_TYPE_RESTRICTED    = 'restricted';
    const ACCESS_TYPE_PRIVATE       = 'private';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Friendship")
     * @ORM\JoinTable(name="users_friendships",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friendship_id", referencedColumnName="id")}
     * )
     */
    private $friendships;

    /**
     * @ORM\OneToMany(targetEntity="Ownership", mappedBy="owner")
     */
    private $ownerships;

    /**
     * @ORM\OneToMany(targetEntity="Rental", mappedBy="userFrom")
     */
    private $thingsRent;

    /**
     * @ORM\OneToMany(targetEntity="Rental", mappedBy="userTo")
     */
    private $thingsLent;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="date")
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1)
     * @todo: add choice validation constraint
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_image", type="string", length=255, nullable=true)
     */
    private $profileImage;

    /**
     * @var string
     *
     * @ORM\Column(name="activated", type="boolean")
     */
    private $activated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_date", type="datetime")
     */
    private $registeredDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * The user's access level. Indicates if everyone may see his profile information or not.
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices = {User::ACCESS_TYPE_PRIVATE, User::ACCESS_TYPE_RESTRICTED, User::ACCESS_TYPE_PUBLIC})
     */
    private $accessLevel;

    /**
     * @ORM\ManyToMany(targetEntity="Conversation")
     * @ORM\JoinTable(name="users_conversations",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="conversation_id", referencedColumnName="id")}
     * ))
     */
    private $conversations;

    /**
     * Constructor
     */
    public function __construct() {
        $this->friendships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ownerships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->activated = false;
        $this->accessLevel = User::ACCESS_TYPE_PRIVATE;
    }

    /**
     * Is the user a friend of the current user.
     * @param UserInterface $user
     * @return bool True if the passed user is a friend of the current user alse false.
     */
    public function isFriendOf(UserInterface $user) {
        /** @var Friendship $friendship */
        foreach($this->getFriendships() as $friendship) {
            if ($friendship->getOtherUser($this) === $user) {
                return true;
            }
        }

        return false;
    }

    /**
     * @ORM\PrePersist
     */
    public function setRegisteredDateValue()
    {
        $this->registeredDate = new \DateTime();
    }

    /**
     ************************
     * Profile image helpers
     ************************
     */

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $profileImageFile;

    public function setProfileImageFile(UploadedFile $file = null)
    {
        $this->profileImageFile = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getProfileImageFile()
    {
        return $this->profileImageFile;
    }

    /**
     ************************
     * Profile image helpers end
     ************************
     */

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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime 
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set profileImage
     *
     * @param string $profileImage
     * @return User
     */
    public function setProfileImage($profileImage)
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * Get profileImage
     *
     * @return string 
     */
    public function getProfileImage()
    {
        return $this->profileImage;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     * @return User
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return boolean
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set registeredDate
     *
     * @param \DateTime $registeredDate
     * @return User
     */
    public function setRegisteredDate($registeredDate)
    {
        $this->registeredDate = $registeredDate;

        return $this;
    }

    /**
     * Get registeredDate
     *
     * @return \DateTime 
     */
    public function getRegisteredDate()
    {
        return $this->registeredDate;
    }


    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * Add friendships
     *
     * @param \Swot\NetworkBundle\Entity\Friendship $friendships
     * @return User
     */
    public function addFriendship(\Swot\NetworkBundle\Entity\Friendship $friendships)
    {
        $this->friendships[] = $friendships;

        return $this;
    }

    /**
     * Remove friendships
     *
     * @param \Swot\NetworkBundle\Entity\Friendship $friendships
     */
    public function removeFriendship(\Swot\NetworkBundle\Entity\Friendship $friendships)
    {
        $this->friendships->removeElement($friendships);
    }

    /**
     * Get friendships
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriendships()
    {
        return $this->friendships;
    }

    /**
     * Add ownerships
     *
     * @param \Swot\NetworkBundle\Entity\Ownership $ownerships
     * @return User
     */
    public function addOwnership(\Swot\NetworkBundle\Entity\Ownership $ownerships)
    {
        $this->ownerships[] = $ownerships;

        return $this;
    }

    /**
     * Remove ownerships
     *
     * @param \Swot\NetworkBundle\Entity\Ownership $ownerships
     */
    public function removeOwnership(\Swot\NetworkBundle\Entity\Ownership $ownerships)
    {
        $this->ownerships->removeElement($ownerships);
    }

    /**
     * Get ownerships
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnerships()
    {
        return $this->ownerships;
    }

    /**
     * Add thingsRent
     *
     * @param \Swot\NetworkBundle\Entity\Rental $thingsRent
     * @return User
     */
    public function addThingsRent(\Swot\NetworkBundle\Entity\Rental $thingsRent)
    {
        $this->thingsRent[] = $thingsRent;

        return $this;
    }

    /**
     * Remove thingsRent
     *
     * @param \Swot\NetworkBundle\Entity\Rental $thingsRent
     */
    public function removeThingsRent(\Swot\NetworkBundle\Entity\Rental $thingsRent)
    {
        $this->thingsRent->removeElement($thingsRent);
    }

    /**
     * Get thingsRent
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThingsRent()
    {
        return $this->thingsRent;
    }

    /**
     * Add thingsLent
     *
     * @param \Swot\NetworkBundle\Entity\Rental $thingsLent
     * @return User
     */
    public function addThingsLent(\Swot\NetworkBundle\Entity\Rental $thingsLent)
    {
        $this->thingsLent[] = $thingsLent;

        return $this;
    }

    /**
     * Remove thingsLent
     *
     * @param \Swot\NetworkBundle\Entity\Rental $thingsLent
     */
    public function removeThingsLent(\Swot\NetworkBundle\Entity\Rental $thingsLent)
    {
        $this->thingsLent->removeElement($thingsLent);
    }

    /**
     * Get thingsLent
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThingsLent()
    {
        return $this->thingsLent;
    }

    /**
     * Set accessLevel
     *
     * @param string $accessLevel
     * @return User
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    /**
     * Get accessLevel
     *
     * @return string 
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
    }

    /**
     * Get conversations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConversations()
    {
        return $this->conversations;
    }

    /**
     * Add conversations
     *
     * @param \Swot\NetworkBundle\Entity\Conversation $conversations
     * @return User
     */
    public function addConversation(\Swot\NetworkBundle\Entity\Conversation $conversations)
    {
        if(!$this->getConversations()->contains($conversations))
            $this->conversations[] = $conversations;

        return $this;
    }

    /**
     * Remove conversations
     *
     * @param \Swot\NetworkBundle\Entity\Conversation $conversations
     */
    public function removeConversation(\Swot\NetworkBundle\Entity\Conversation $conversations)
    {
        $this->conversations->removeElement($conversations);
    }
}
