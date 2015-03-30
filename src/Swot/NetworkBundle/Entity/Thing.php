<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Swot\NetworkBundle\Security\AccessType;

/**
 * Thing
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\ThingRepository")
 */
class Thing implements UserInterface
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
     * @ORM\Column(name="network_access_token", type="string", length=255)
     */
    private $networkAccessToken;

    /**
     * @ORM\OneToOne(targetEntity="Ownership", mappedBy="thing", cascade={"persist"})
     */
    private $ownership;

    /**
     * @ORM\OneToMany(targetEntity="Rental", mappedBy="thing")
     */
    private $rentals;

    /**
     * @ORM\OneToMany(targetEntity="Swot\FormMapperBundle\Entity\Action", mappedBy="thing")
     */
    private $functions;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices = {AccessType::ACCESS_TYPE_PRIVATE, AccessType::ACCESS_TYPE_RESTRICTED, AccessType::ACCESS_TYPE_PUBLIC})
     */
    private $accessType;

    /**
     * @ORM\OneToMany(targetEntity="ThingStatusUpdate", mappedBy="thing")
     */
    private $statusUpdates;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_image", type="string", length=255, nullable=true)
     */
    private $profileImage;

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
     * 
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
     * @param string $networkAccessToken
     * @return Thing
     */
    public function setNetworkAccessToken($networkAccessToken)
    {
        $this->networkAccessToken = $networkAccessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string 
     */
    public function getNetworkAccessToken()
    {
        return $this->networkAccessToken;
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
        $this->accessType = AccessType::ACCESS_TYPE_PRIVATE;
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

    /**
     * Get functions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Set accessType
     *
     * @param string $accessType
     * @return Thing
     */
    public function setAccessType($accessType)
    {
        $this->accessType = $accessType;

        return $this;
    }

    /**
     * Get accessType
     *
     * @return string 
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * Add functions
     *
     * @param \Swot\FormMapperBundle\Entity\Action $functions
     * @return Thing
     */
    public function addFunction(\Swot\FormMapperBundle\Entity\Action $functions)
    {
        $this->functions[] = $functions;

        return $this;
    }

    /**
     * Remove functions
     *
     * @param \Swot\FormMapperBundle\Entity\Action $functions
     */
    public function removeFunction(\Swot\FormMapperBundle\Entity\Action $functions)
    {
        $this->functions->removeElement($functions);
    }

    /**
     * Add statusUpdates
     *
     * @param \Swot\NetworkBundle\Entity\ThingStatusUpdate $statusUpdates
     * @return Thing
     */
    public function addStatusUpdate(\Swot\NetworkBundle\Entity\ThingStatusUpdate $statusUpdates)
    {
        $this->statusUpdates[] = $statusUpdates;

        return $this;
    }

    /**
     * Remove statusUpdates
     *
     * @param \Swot\NetworkBundle\Entity\ThingStatusUpdate $statusUpdates
     */
    public function removeStatusUpdate(\Swot\NetworkBundle\Entity\ThingStatusUpdate $statusUpdates)
    {
        $this->statusUpdates->removeElement($statusUpdates);
    }

    /**
     * Get statusUpdates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStatusUpdates()
    {
        return $this->statusUpdates;
    }

    /**
     * Set profileImage
     *
     * @param string $profileImage
     * @return Thing
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
        return array();
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
        // TODO: Implement getPassword() method.
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
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getId();
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
}
