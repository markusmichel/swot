<?php

namespace Swot\NetworkBundle\Services\Manager;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Swot\FormMapperBundle\Entity\AbstractConstraint;
use Swot\FormMapperBundle\Entity\Action;
use Swot\FormMapperBundle\Entity\Parameter\Parameter;
use Swot\NetworkBundle\Entity\Ownership;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\ThingStatusUpdate;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Exception\ThingIsUnavailableException;
use Swot\NetworkBundle\Services\CurlManager;
use League\Url\Url;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 *
 *
 * Class ThingManager
 * @package Swot\NetworkBundle\Services\Manager
 *
 * @DI\Service("swot.manager.thing")
 */
class ThingManager {

    /** @var EntityManager */
    private $em;

    /** @var CurlManager*/
    private $curlManager;

    private $deregisterRoute;
    private $devMode;

    /**
     * @DI\InjectParams({
     *      "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "cM" = @DI\Inject("services.curl_manager"),
     *      "deregisterRoute" = @DI\Inject("%thing.api.deregister%"),
     *      "devMode" = @DI\Inject("%swot.development.mode%")
     * })
     * @param EntityManager $em
     * @param CurlManager $cM
     * @param String $deregisterRoute
     */
    public function __construct(EntityManager $em, CurlManager $cM, $deregisterRoute, $devMode) {
        $this->em = $em;
        $this->curlManager = $cM;
        $this->deregisterRoute = $deregisterRoute;
        $this->devMode = $devMode;
    }

    public function createOwnership(Thing $thing, User $user) {
        $ownership = new Ownership();
        $ownership->setThing($thing);
        $ownership->setOwner($user);
        $user->addOwnership($ownership);
        $thing->setOwnership($ownership);

        return $ownership;
    }

    /**
     * Completely removed a thing and all it's related entities (Ownership, Rentals, Actions)
     * from the database.
     *
     * @param Thing $thing
     */
    public function remove(Thing $thing) {

        // check if real thing is used
        if($this->devMode == 0){
            $url = $thing->getBaseApiUrl() . $this->deregisterRoute;
            $formattedUrl = URL::createFromUrl($url);

            try{
                $deregisterResponse = $this->curlManager->getCurlResponse($formattedUrl->__toString(), true, $thing->getOwnerToken());
            } catch (Exception $e){
                throw new ThingIsUnavailableException("Unable to delete the selected Thing");
            }


            if($deregisterResponse->statusCode != 200)
                throw new ThingIsUnavailableException("Unable to delete the selected Thing");
        }
        $this->em->remove($thing);
        $this->em->flush();
    }

    /**
     * @param Thing $thing
     */
    public function removeFunctions(Thing $thing)
    {
        /** @var Action $func */
        foreach($thing->getFunctions() as $func){
            $this->em->remove($func);
        }
        $thing->getFunctions()->clear();
        $this->em->persist($thing);
        $this->em->flush();
    }

    /**
     * @param Action $function The thing function to execute
     * @param $accessToken String The authentication token to communcicate with the thing
     * @return mixed string Thing response
     */
    public function activateFunction(Action $function, $accessToken)
    {
        $parameters = array();
        /** @var Parameter $param */
        foreach($function->getParameters() as $param) {
            $parameters[$param->getName()] = $param->getValue();
        }

        // check if real thing is used
        if($this->devMode == 0)
        {
            $url = URL::createFromUrl($function->getUrl());
            $query = $url->getQuery();
            $query->set($parameters);
            $url->setQuery($query);
            $url = $url->__toString();
        }else{
            $url = "http://www.google.de";
        }


        try{
            return $this->curlManager->getCurlResponse($url, true, $accessToken);
        }catch(Exception $e){
            throw new ThingIsUnavailableException("Action could not be executed");
        }
    }
}