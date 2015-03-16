<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FunctionParameter
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\FunctionParameterRepository")
 */
class FunctionParameter
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

    private $value;

    public function setValue($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function __toString() {
        return $this->getValue();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="ThingFunction", inversedBy="parameters")
     * @ORM\JoinColumn(name="function_id", referencedColumnName="id")
     */
    private $thingFunction;

    /**
     * @ORM\OneToMany(targetEntity="ParameterConstraint", mappedBy="functionParameter")
     */
    private $constraints;

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
     * @return FunctionParameter
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
     * Set type
     *
     * @param string $type
     * @return FunctionParameter
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->constraints = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set thingFunction
     *
     * @param \Swot\NetworkBundle\Entity\ThingFunction $thingFunction
     * @return FunctionParameter
     */
    public function setThingFunction(\Swot\NetworkBundle\Entity\ThingFunction $thingFunction = null)
    {
        $this->thingFunction = $thingFunction;

        return $this;
    }

    /**
     * Get thingFunction
     *
     * @return \Swot\NetworkBundle\Entity\ThingFunction 
     */
    public function getThingFunction()
    {
        return $this->thingFunction;
    }

    /**
     * Add constraints
     *
     * @param \Swot\NetworkBundle\Entity\ParameterConstraint $constraints
     * @return FunctionParameter
     */
    public function addConstraint(\Swot\NetworkBundle\Entity\ParameterConstraint $constraints)
    {
        $this->constraints[] = $constraints;

        return $this;
    }

    /**
     * Remove constraints
     *
     * @param \Swot\NetworkBundle\Entity\ParameterConstraint $constraints
     */
    public function removeConstraint(\Swot\NetworkBundle\Entity\ParameterConstraint $constraints)
    {
        $this->constraints->removeElement($constraints);
    }

    /**
     * Get constraints
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConstraints()
    {
        return $this->constraints;
    }
}
