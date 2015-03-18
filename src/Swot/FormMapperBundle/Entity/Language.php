<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language
 *
 * @ORM\Entity
 */
class Language extends AbstractConstraint
{
    /**
     * @inheritDoc
     */
    public function createConstraint()
    {
        return new \Symfony\Component\Validator\Constraints\Language(array(
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
