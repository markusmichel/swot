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
     * /api/v1/thing/messages
     *
     * Receives a message from a thing.
     * Saves it to the thing's message list.
     *
     * Requires:
     *      - message: Message to send
     *
     * @RequestParam(name="message", requirements=".+", strict=true, allowBlank=false, description="Message to post")
     *
     * @param ParamFetcher $fetcher
     * @return Response
     */
    public function postMessageAction(ParamFetcher $fetcher) {
        /** @var Thing $thing */
        $thing = $this->getUser();
        $messageStr = $fetcher->get('message');

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
     * @RequestParam(name="functions", strict=true, allowBlank=false, description="The thing's functions")
     *
     * @Post("/functions/update")
     *
     * @param ParamFetcher $fetcher
     */
    public function postThingFunctionsUpdateAction(ParamFetcher $fetcher) {
        /** @var Thing $thing */
        $thing = $this->getUser();

        // @todo: implement
    }

}
