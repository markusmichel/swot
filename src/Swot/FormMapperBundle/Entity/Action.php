<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swot\FormMapperBundle\Entity\AbstractParameter;
use Swot\NetworkBundle\Fixtures\ThingFixtures;

/**
 * Function
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Action
{

    /**
     * @param string $accessToken
     * @return mixed|string
     */
    public function activate($accessToken = "") {
        $parameters = array();
        /** @var AbstractParameter $param */
        foreach($this->getParameters() as $param) {
            $parameters[$param->getName()] = $param->getValue();
        }

        $parameters["token"] = $accessToken;

        $url = $this->getUrl() . "?" . http_build_query($parameters);
        $url = "http://www.google.de";

        // Callback will use reference to this variable
        $response = null;

        $curl = new \Zebra_cURL();
        $curl->get($url, function($result) use (&$response) {
            // everything went well at cURL level
            if ($result->response[1] == CURLE_OK) {

                // if server responded with code 200 (meaning that everything went well)
                // see http://httpstatus.es/ for a list of possible response codes
                if ($result->info['http_code'] == 200) {

                    $body = $result->body;

                    // see all the returned data
//                    print_r('<pre>');
//                    print_r($result);
//                    die();

                    $response = ThingFixtures::$activateFunctionResponse;

                    // @todo: change me
//                    $response = json_decode($body);
                    $response = json_decode($response);


                }
                // @todo: create exception
                else die('Server responded with code ' . $result->info['http_code']);


            }

            // something went wrong
            // ($result still contains all data that could be gathered)
            // @todo: create exception
            else die('cURL responded with: ' . $result->response[0]);
        });

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
     * @ORM\ManyToOne(targetEntity="Swot\NetworkBundle\Entity\Thing", inversedBy="functions")
     * @ORM\JoinColumn(name="thing_id", referencedColumnName="id")
     */
    private $thing;

    /**
     * @ORM\OneToMany(targetEntity="AbstractParameter", mappedBy="action")
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
     * Add parameters
     *
     * @param \Swot\FormMapperBundle\Entity\AbstractParameter $parameters
     * @return ThingFunction
     */
    public function addParameter(\Swot\FormMapperBundle\Entity\AbstractParameter $parameters)
    {
        $this->parameters[] = $parameters;

        return $this;
    }

    /**
     * Remove parameters
     *
     * @param \Swot\FormMapperBundle\Entity\AbstractParameter $parameters
     */
    public function removeParameter(\Swot\FormMapperBundle\Entity\AbstractParameter $parameters)
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

    /**
     * Set thing
     *
     * @param \Swot\NetworkBundle\Entity\Thing $thing
     * @return Action
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
}
