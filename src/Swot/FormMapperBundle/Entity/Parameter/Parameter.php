<?php

namespace Swot\FormMapperBundle\Entity\Parameter;

use Doctrine\ORM\Mapping as ORM;
use Swot\FormMapperBundle\Entity\Parameter\ChoiceParameter;

/**
 * AbstractParameter
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *      "simple_parameter" = "Parameter",
 *      "choice" = "Swot\FormMapperBundle\Entity\Parameter\ChoiceParameter"
 * })
 */
class Parameter
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->constraints = new \Doctrine\Common\Collections\ArrayCollection();
        $this->disabled = false;
        $this->readOnly = false;
        $this->required = true;
    }

    /**
     * Creates a new Parameter Object from json decoded object (Response from thing).
     * Decides it will be a simple arameter choice parameter etc.
     *
     * @param $param
     * @return Parameter
     */
    public static function createParameter($param) {
        $parameter = null;
        switch($param->type) {
            case 'Choice':
                if(isset($param->choices)) {
                    $parameter = new ChoiceParameter();
                    $parameter->setChoices($param->choices);
                    break;
                }

            default:
                $parameter = new Parameter();
                break;
        }

        $parameter->setName($param->name);
        $parameter->setType($param->type);
        if(isset($param->required) && is_bool($param->required)) $parameter->setRequired($param->required);
        if(isset($param->readOnly) && is_bool($param->readOnly)) $parameter->setReadOnly($param->readOnly);
        if(isset($param->expanded) && is_bool($param->expanded)) $parameter->setExpanded($param->expanded);
        if(isset($param->multiple) && is_bool($param->multiple)) $parameter->setMultiple($param->multiple);
        if(isset($param->defaultValue)) $parameter->setDefaultValue($param->defaultValue);

        return $parameter;
    }

    /**
     * Creates an array compatible to Symfony's form mapper component (3rd argument of "add").
     *
     * @see FormBuilderInterface
     * @return array
     */
    public function toFormMapperArray() {
        $this->setValue($this->getDefaultValue());
        return array(
            'label' => $this->getName(),
            'constraints' => $this->getConstraintsAsArray(),
            'required' => true,
            'read_only' => $this->getReadOnly(),
        );
    }

    /**
     * Return an array containing all the parameter's constraints.
     * The constraints are compatible with the Symfony form component and are generated in the constraints'
     * createContraint methods.
     *
     * @see \Symfony\Component\Validator\Constraints
     * @see \Swot\FormMapperBundle\Entity\AbstractConstraint
     * @return array
     */
    public function getConstraintsAsArray() {
        $constraints = array();

        /** @var AbstractConstraint $constraint */
        foreach($this->getConstraints() as $constraint) {
            $c = $constraint->createConstraint();
            if($c !== null) $constraints[] = $c;
        }

        return $constraints;
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="is_disabled", type="boolean")
     */
    private $disabled;

    /**
     * @ORM\Column(name="is_required", type="boolean")
     */
    protected $required;

    /**
     * @ORM\Column(name="is_read_only", type="boolean")
     */
    protected $readOnly;

    /**
     * @todo: check if it is needed to url en/decode the value
     * @var string
     *
     * @ORM\Column(name="default_value", type="string", length=255, nullable=true)
     */
    protected $defaultValue;

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
     * @ORM\ManyToOne(targetEntity="\Swot\FormMapperBundle\Entity\Action", inversedBy="parameters")
     * @ORM\JoinColumn(name="function_id", referencedColumnName="id")
     */
    private $action;

    /**
     * @ORM\OneToMany(targetEntity="\Swot\FormMapperBundle\Entity\AbstractConstraint", mappedBy="functionParameter")
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
     * @return AbstractParameter
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
     * @return AbstractParameter
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
     * Add constraints
     *
     * @param \Swot\FormMapperBundle\Entity\AbstractConstraint $constraints
     * @return $this
     */
    public function addConstraint(\Swot\FormMapperBundle\Entity\AbstractConstraint $constraints)
    {
        $this->constraints[] = $constraints;

        return $this;
    }

    /**
     * Remove constraints
     *
     * @param \Swot\FormMapperBundle\Entity\AbstractConstraint $constraints
     */
    public function removeConstraint(\Swot\FormMapperBundle\Entity\AbstractConstraint $constraints)
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

    /**
     * Set action
     *
     * @param \Swot\FormMapperBundle\Entity\Action $action
     * @return AbstractParameter
     */
    public function setAction(\Swot\FormMapperBundle\Entity\Action $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \Swot\FormMapperBundle\Entity\Action 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return Parameter
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean 
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set readOnly
     *
     * @param boolean $readOnly
     * @return Parameter
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * Get readOnly
     *
     * @return boolean 
     */
    public function getReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return Parameter
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set defaultValue
     *
     * @param string $defaultValue
     * @return Parameter
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Get defaultValue
     *
     * @return string 
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
