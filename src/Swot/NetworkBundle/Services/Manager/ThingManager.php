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
    public $em;

    /** @var CurlManager*/
    public $curlManager;

    public $deregisterRoute;

    /**
     * @DI\InjectParams({
     *      "em" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "cM" = @DI\Inject("services.curl_manager"),
     *      "deregisterRoute" = @DI\Inject("%thing.api.deregister%")
     * })
     * @param EntityManager $em
     * @param CurlManager $cM
     * @param String $deregisterRoute
     */
    public function __construct(EntityManager $em, CurlManager $cM, $deregisterRoute) {
        $this->em = $em;
        $this->curlManager = $cM;
        $this->deregisterRoute = $deregisterRoute;
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
        //@TODO: $userCurlToDelete only for development
        $useCurlToDelete = 0;
        if($useCurlToDelete == 1){
            $url = $thing->getBaseApiUrl() . $this->deregisterRoute;
            $formattedUrl = URL::createFromUrl($url);

            $deregisterResponse = $this->curlManager->getCurlResponse($formattedUrl->__toString(), true, $thing->getOwnerToken());

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

        //@TODO: for real use use this url
        $url = URL::createFromUrl($function->getUrl());
        $query = $url->getQuery();
        $query->set($parameters);
        $url->setQuery($query);
        $url = $url->__toString();

        //@TODO: only for development
        $url = "http://www.google.de";

        return $this->curlManager->getCurlResponse($url, true, $accessToken);

    }
}