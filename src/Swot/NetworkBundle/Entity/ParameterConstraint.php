<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParameterConstraint
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\ParameterConstraintRepository")
 */
class ParameterConstraint
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="FunctionParameter", inversedBy="constraints")
     * @ORM\JoinColumn(name="parameter_id", referencedColumnName="id")
     */
    private $functionParameter;

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
     * Set type
     *
     * @param string $type
     * @return ParameterConstraint
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
     * Set message
     *
     * @param string $message
     * @return ParameterConstraint
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
     * Set functionParameter
     *
     * @param \Swot\NetworkBundle\Entity\FunctionParameter $functionParameter
     * @return ParameterConstraint
     */
    public function setFunctionParameter(\Swot\NetworkBundle\Entity\FunctionParameter $functionParameter = null)
    {
        $this->functionParameter = $functionParameter;

        return $this;
    }

    /**
     * Get functionParameter
     *
     * @return \Swot\NetworkBundle\Entity\FunctionParameter 
     */
    public function getFunctionParameter()
    {
        return $this->functionParameter;
    }
}
