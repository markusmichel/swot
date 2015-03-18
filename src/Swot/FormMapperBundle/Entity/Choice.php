<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Choice
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Choice extends AbstractConstraint
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
     * @var array
     *
     * @ORM\Column(name="choices", type="json_array")
     */
    private $choices;

    /**
     * @var boolean
     *
     * @ORM\Column(name="multiple", type="boolean")
     */
    private $multiple;

    /**
     * @var integer
     *
     * @ORM\Column(name="min", type="integer")
     */
    private $min;

    /**
     * @var integer
     *
     * @ORM\Column(name="max", type="integer")
     */
    private $max;

    /**
     * @var string
     *
     * @ORM\Column(name="multipleMessage", type="string", length=255)
     */
    private $multipleMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="minMessage", type="string", length=255)
     */
    private $minMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="MaxMessage", type="string", length=255)
     */
    private $maxMessage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="strict", type="boolean")
     */
    private $strict;


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
     * @return Choice
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
     * Set multiple
     *
     * @param boolean $multiple
     * @return Choice
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
     * Set min
     *
     * @param integer $min
     * @return Choice
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get min
     *
     * @return integer 
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Set max
     *
     * @param integer $max
     * @return Choice
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return integer 
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set multipleMessage
     *
     * @param string $multipleMessage
     * @return Choice
     */
    public function setMultipleMessage($multipleMessage)
    {
        $this->multipleMessage = $multipleMessage;

        return $this;
    }

    /**
     * Get multipleMessage
     *
     * @return string 
     */
    public function getMultipleMessage()
    {
        return $this->multipleMessage;
    }

    /**
     * Set minMessage
     *
     * @param string $minMessage
     * @return Choice
     */
    public function setMinMessage($minMessage)
    {
        $this->minMessage = $minMessage;

        return $this;
    }

    /**
     * Get minMessage
     *
     * @return string 
     */
    public function getMinMessage()
    {
        return $this->minMessage;
    }

    /**
     * Set maxMessage
     *
     * @param string $maxMessage
     * @return Choice
     */
    public function setMaxMessage($maxMessage)
    {
        $this->maxMessage = $maxMessage;

        return $this;
    }

    /**
     * Get maxMessage
     *
     * @return string 
     */
    public function getMaxMessage()
    {
        return $this->maxMessage;
    }

    /**
     * Set strict
     *
     * @param boolean $strict
     * @return Choice
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;

        return $this;
    }

    /**
     * Get strict
     *
     * @return boolean 
     */
    public function getStrict()
    {
        return $this->strict;
    }
}
