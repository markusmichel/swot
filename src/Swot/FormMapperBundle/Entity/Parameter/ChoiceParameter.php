<?php

namespace Swot\FormMapperBundle\Entity\Parameter;

use Doctrine\ORM\Mapping as ORM;
use Swot\FormMapperBundle\Entity\AbstractConstraint;

/**
 * ChoiceParameter
 *
 * @ORM\Entity
 */
class ChoiceParameter extends Parameter
{
    public function __construct()
    {
        parent::__construct();
        $this->expanded = false;
        $this->multiple = false;
    }

    /**
     * @inheritdoc
     */
    public function toFormMapperArray() {
        $arr = parent::toFormMapperArray();

        return array_merge($arr, array(
            'choices' => $this->getChoices(),
            'expanded' => $this->getExpanded(),
            'multiple' => $this->getMultiple(),
        ));
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
     * @var array
     *
     * @ORM\Column(name="choices", type="simple_array")
     */
    protected $choices;

    /**
     * @var boolean
     */
    protected $required;

    /**
     * @var boolean
     */
    protected $readOnly;

    /**
     * @ORM\Column(name="is_expanded", type="boolean")
     */
    protected $expanded;

    /**
     * @ORM\Column(name="is_multiple", type="boolean")
     */
    protected $multiple;

    /**
     * @var string
     */
    protected $defaultValue;


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
     * Set choices
     *
     * @param array $choices
     * @return ChoiceParameter
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Get choices
     *
     * @return array 
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return ChoiceParameter
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
     * Set readOnly
     *
     * @param boolean $readOnly
     * @return ChoiceParameter
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
     * Set expanded
     *
     * @param boolean $expanded
     * @return ChoiceParameter
     */
    public function setExpanded($expanded)
    {
        $this->expanded = $expanded;

        return $this;
    }

    /**
     * Get expanded
     *
     * @return boolean 
     */
    public function getExpanded()
    {
        return $this->expanded;
    }

    /**
     * Set multiple
     *
     * @param boolean $multiple
     * @return ChoiceParameter
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get multiple
     *
     * @return boolean 
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set defaultValue
     *
     * @param string $defaultValue
     * @return ChoiceParameter
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
