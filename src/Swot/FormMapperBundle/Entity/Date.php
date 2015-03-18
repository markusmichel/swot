<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Date
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Date extends AbstractConstraint
{
    /**
     * @inheritDoc
     */
    public function createConstraint()
    {
        return new \Symfony\Component\Validator\Constraints\Date(array(
            'message' => $this->getMessage(),
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
