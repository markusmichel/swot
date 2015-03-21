<?php

namespace Swot\NetworkBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\ThingStatusUpdate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ThingRestController
 * @package Swot\NetworkBundle\Controller
 * @Prefix("/api/v1/thing")
 */
class ThingRestController extends FOSRestController
{
    /**
     * Checks if a thing's network access token is valid.
     *
     * @param $token
     * @return bool
     */
    protected function assertThingAccessTokenValid(Thing $thing, $token) {
        // @todo: implement
        $valid = true;
        if($valid === false) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @param $id
     * @return Thing
     * @throws EntityNotFoundException
     */
    protected function getThing($id) {
        /** @var Thing $thing */
        $thing = $this->getDoctrine()->getRepository("SwotNetworkBundle:Thing")->find($id);
        if($thing === null) throw new EntityNotFoundException();

        return $thing;
    }

    /**
     * /api/v1/message
     *
     * Receives a message from a thing.
     * Saves it to the thing's message list.
     *
     * Requires:
     *      - id: thingId
     *      - access_token: Thing's network access token
     *      - message: Message to send
     *
     * @RequestParam(name="id", requirements="[0-9]+", strict=true, allowBlank=false, description="Id of the thing")
     * @RequestParam(name="message", requirements=".+", strict=true, allowBlank=false, description="Message to post")
     * @RequestParam(name="access_token", requirements=".{5,}", strict=true, allowBlank=false, description="Network access token of the thing")
     *
     * @param ParamFetcher $fetcher
     * @return Response
     *
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function postMessageAction(ParamFetcher $fetcher) {
        $thingId = $fetcher->get('id');
        $thing = $this->getThing($thingId);
        $messageStr = $fetcher->get('message');
        $accessToken = $fetcher->get('access_token');

        // Assert access token is valid
        $this->assertThingAccessTokenValid($thing, $accessToken);

        $message = new ThingStatusUpdate();
        $message->setMessage($messageStr);
        $message->setThing($thing);

        $this->getDoctrine()->getManager()->persist($message);
        $this->getDoctrine()->getManager()->persist($thing);
        $this->getDoctrine()->getManager()->flush();

        return $this->handleView(new View(array(
            "code" => Response::HTTP_OK,
            "message" => "Message saved",
        )));
    }

    /**
     *
     * @RequestParam(name="id", requirements="[0-9]+", strict=true, allowBlank=false, description="Id of the thing")
     * @RequestParam(name="access_token", requirements=".{5,}", strict=true, allowBlank=false, description="Network access token of the thing")
     * @RequestParam(name="functions", strict=true, allowBlank=false, description="The thing's functions")
     *
     * @Post("/functions/update")
     *
     * @param ParamFetcher $fetcher
     *
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function postThingFunctionsUpdateAction(ParamFetcher $fetcher) {
        $thing = $this->getThing($fetcher->get('id'));
        $this->assertThingAccessTokenValid($thing, $fetcher->get('access_token'));

        // @todo: implement
    }

}