<?php

namespace Swot\FormMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swot\FormMapperBundle\Entity\Parameter\Parameter;
use Swot\NetworkBundle\Fixtures\ThingFixtures;
use Swot\NetworkBundle\Services\CurlManager;
use League\Url\Url;

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
        /** @var Parameter $param */
        foreach($this->getParameters() as $param) {
            $parameters[$param->getName()] = $param->getValue();
        }

        $parameters["token"] = $accessToken;

        $url = URL::createFromUrl($this->getUrl());
        $query = $url->getQuery();
        $query->set($parameters);
        $url->setQuery($query);
        $url = $url->__toString();

        //@TODO: only for development
        $url = "http://www.google.de";

        //@TODO: after development uncomment these lines
        /** @var CurlManager $curlManager */
        //$curlManager = $this->get('services.curl_manager');
        //return $curlManager->getCurlResponse($url);


        // @TODO: delete the following lines after development
        // Callback will use reference to this variable
        $response = null;

        $curl = new \Zebra_cURL();
        $curl->get($url, function($result) use (&$response) {
            // everything went well at cURL level
            if ($result->response[1] == CURLE_OK) {

                // if server responded with code 200 (meaning that everything went well)
                // see http://httpstatus.es/ for a list of possible response codes
                if ($result->info['http_code'] == 200) {

                    $response = json_decode($result->body);

                    //@TODO: only for dev
                    $response = ThingFixtures::$activateFunctionResponse;
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
     * @ORM\OneToMany(targetEntity="Swot\FormMapperBundle\Entity\Parameter\Parameter", mappedBy="action", cascade={"persist"})
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
     * @param \Swot\FormMapperBundle\Entity\Parameter\Parameter $parameters
     * @return ThingFunction
     */
    public function addParameter(\Swot\FormMapperBundle\Entity\Parameter\Parameter $parameters)
    {
        $this->parameters[] = $parameters;

        return $this;
    }

    /**
     * Remove parameters
     *
     * @param \Swot\FormMapperBundle\Entity\Parameter $parameters
     */
    public function removeParameter(\Swot\FormMapperBundle\Entity\Parameter\Parameter $parameters)
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
