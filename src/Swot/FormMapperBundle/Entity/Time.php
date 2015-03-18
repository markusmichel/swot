<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Time
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Time extends AbstractConstraint
{
    /**
     * @inheritDoc
     */
    public function createConstraint()
    {
        // TODO: Implement createConstraint() method.
    }

    /**
     * @inheritDoc
     */
    public function initFromArray(array $arr) {
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
