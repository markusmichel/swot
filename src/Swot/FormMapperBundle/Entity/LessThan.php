<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LessThan
 *
 * @ORM\Entity
 */
class LessThan extends AbstractConstraint
{
    /**
     * @inheritDoc
     */
    public function createConstraint()
    {
        return new \Symfony\Component\Validator\Constraints\LessThan(array(
            'message' => $this->getMessage(),
            'value' => $this->getMaxValue(),
        ));
    }

    /**
     * @inheritDoc
     */
    public function init($obj) {
        // TODO: Implement createConstraint() method.
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
     * @var float
     *
     * @ORM\Column(name="max_value", type="float")
     */
    private $maxValue;


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
     * Set maxValue
     *
     * @param float $maxValue
     * @return LessThan
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * Get maxValue
     *
     * @return float 
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }
}
