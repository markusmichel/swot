<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GreaterThan
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class GreaterThan extends AbstractConstraint
{
    /**
     * @inheritDoc
     */
    public function createConstraint()
    {
        return new \Symfony\Component\Validator\Constraints\GreaterThan(array(
            'message' => $this->getMessage(),
            'value' => $this->getMinValue(),
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
     * @ORM\Column(name="manValue", type="float")
     */
    private $minValue;


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
     * Set manValue
     *
     * @param float $minValue
     * @return GreaterThan
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * Get manValue
     *
     * @return float 
     */
    public function getMinValue()
    {
        return $this->minValue;
    }
}
