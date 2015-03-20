<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\ThingStatusUpdate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThingRestController extends Controller
{

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
     * Error codes
     * 405 (method not allowed)
     *      - missing id
     *      - missing message
     *
     * 401 (unauthorized)
     *      - missing access token
     *      - invalid access token
     *      - access token does not match device
     *
     * @param Request $request
     * @return Response
     */
    public function newMessageAction(Request $request) {

        // Assert request has id
        if(!$request->request->has('id')) {
            $response = new JsonResponse(array(
                "code" => Response::HTTP_METHOD_NOT_ALLOWED,
                "message" => "You have to specify an id",
            ));
            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
            return $response;
        }

        // Assert access token exists
        if(!$request->request->has('access_token')) {
            $response = new JsonResponse(array(
                "code" => Response::HTTP_UNAUTHORIZED,
                "message" => "You have to specify an access token",
            ));
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        // Assert message exists
        if(!$request->request->has('message')) {
            $response = new JsonResponse(array(
                "code" => Response::HTTP_METHOD_NOT_ALLOWED,
                "message" => "You have to specify a message",
            ));
            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
            return $response;
        }

        $thingId = $request->request->get('id');
        $thing = $this->getDoctrine()->getRepository("SwotNetworkBundle:Thing")->find($thingId);
        $messageStr = $request->request->get('message');
        $accessToken = $request->request->get('access_token');

        // Assert thing exists
        if($thing === null) {
            $response = new JsonResponse(array(
                "code" => Response::HTTP_METHOD_NOT_ALLOWED,
                "message" => "No such thing",
            ));
            $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
            return $response;
        }

        // @todo: check authentication / access token

        $message = new ThingStatusUpdate();
        $message->setMessage($messageStr);
        $message->setThing($thing);

        $this->getDoctrine()->getManager()->persist($message);
        $this->getDoctrine()->getManager()->persist($thing);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(array(
            "status" => Response::HTTP_OK,
            "message" => "Message saved",
        ));
    }

}
