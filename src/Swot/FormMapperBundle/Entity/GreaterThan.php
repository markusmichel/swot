<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GreaterThan
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class GreaterThan extends AbstractParameter
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
     * @var float
     *
     * @ORM\Column(name="manValue", type="float")
     */
    private $manValue;


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
     * @param float $manValue
     * @return GreaterThan
     */
    public function setManValue($manValue)
    {
        $this->manValue = $manValue;

        return $this;
    }

    /**
     * Get manValue
     *
     * @return float 
     */
    public function getManValue()
    {
        return $this->manValue;
    }
}
