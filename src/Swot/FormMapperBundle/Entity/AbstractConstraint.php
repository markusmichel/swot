<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParameterConstraint
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *      "not_null" = "NotNull",
 *      "choice" = "Choice",
 *      "country" = "Country",
 *      "date" = "Date",
 *      "datetime" = "DateTime",
 *      "greater_than" = "GreaterThan",
 *      "language" = "Language",
 *      "less_than" = "LessThan",
 *      "locale" = "Locale",
 *      "not_blank" = "NotBlank",
 *      "not_null" = "NotNull",
 *      "time" = "Time"
 * })
 */
abstract class AbstractConstraint
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
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="\Swot\FormMapperBundle\Entity\Parameter\Parameter", inversedBy="constraints")
     * @ORM\JoinColumn(name="parameter_id", referencedColumnName="id")
     */
    private $functionParameter;

    /**
     * Creates a Symfony/Doctrine compatible constraint.
     * @see \Symfony\Component\Validator\Constraints
     * @return mixed
     */
    public abstract function createConstraint();

    /**
     * Sets values from PHP array.
     * @param $obj
     * @return mixed
     */
    public abstract function init($obj);

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
     * @return AbstractConstraint
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
     * @param \Swot\FormMapperBundle\Entity\Parameter\Parameter $functionParameter
     * @return AbstractConstraint
     */
    public function setFunctionParameter(\Swot\FormMapperBundle\Entity\Parameter\Parameter $functionParameter = null)
    {
        $this->functionParameter = $functionParameter;

        return $this;
    }

    /**
     * Get functionParameter
     *
     * @return \Swot\FormMapperBundle\Entity\Parameter\Parameter
     */
    public function getFunctionParameter()
    {
        return $this->functionParameter;
    }
}
