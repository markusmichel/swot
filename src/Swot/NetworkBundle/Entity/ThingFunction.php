<?php

namespace Swot\NetworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swot\NetworkBundle\Fixtures\ThingFixtures;

/**
 * Function
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Swot\NetworkBundle\Entity\FunctionRepository")
 */
class ThingFunction
{

    /**
     * @param string $accessToken
     * @return mixed|string
     */
    public function activate($accessToken = "") {
        $parameters = array();
        /** @var FunctionParameter $param */
        foreach($this->getParameters() as $param) {
            $parameters[$param->getName()] = $param->getValue();
        }

        $parameters["token"] = $accessToken;

        $url = $this->getUrl() . "?" . http_build_query($parameters);

        // @TODO: retrieve dynamic values
//        $response = file_get_contents($url);
        $response = ThingFixtures::$activateFunctionResponse;
        $response = json_decode($response);

        return $response;
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="Thing", inversedBy="functions")
     * @ORM\JoinColumn(name="thing_id", referencedColumnName="id")
     */
    private $thing;

    /**
     * @ORM\OneToMany(targetEntity="FunctionParameter", mappedBy="thingFunction")
     */
    private $parameters;

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
     * @return Function
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
     * Set url
     *
     * @param string $url
     * @return Function
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set thing
     *
     * @param \Swot\NetworkBundle\Entity\Thing $thing
     * @return ThingFunction
     */
    public function setThing(\Swot\NetworkBundle\Entity\Thing $thing = null)
    {
        $this->thing = $thing;

        return $this;
    }

    /**
     * Get thing
     *
     * @return \Swot\NetworkBundle\Entity\Thing 
     */
    public function getThing()
    {
        return $this->thing;
    }

    /**
     * Add parameters
     *
     * @param \Swot\NetworkBundle\Entity\FunctionParameter $parameters
     * @return ThingFunction
     */
    public function addParameter(\Swot\NetworkBundle\Entity\FunctionParameter $parameters)
    {
        $this->parameters[] = $parameters;

        return $this;
    }

    /**
     * Remove parameters
     *
     * @param \Swot\NetworkBundle\Entity\FunctionParameter $parameters
     */
    public function removeParameter(\Swot\NetworkBundle\Entity\FunctionParameter $parameters)
    {
        $this->parameters->removeElement($parameters);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
