<?php

namespace Swot\NetworkBundle\Controller;

use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Swot\FormMapperBundle\Entity\Action;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\ThingStatusUpdate;
use Swot\NetworkBundle\Services\CurlManager;
use Swot\NetworkBundle\Services\Manager\ThingManager;
use Swot\NetworkBundle\Services\ThingResponseConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ThingRestController
 * @package Swot\NetworkBundle\Controller
 * @Prefix("/api/v1/thing")
 *
 * @IgnoreAnnotation("apiGroup")
 * @IgnoreAnnotation("apiName")
 * @IgnoreAnnotation("apiParam")
 * @IgnoreAnnotation("apiSuccess")
 * @IgnoreAnnotation("apiDescription")
 * @IgnoreAnnotation("apiSuccessExample")
 * @IgnoreAnnotation("apiVersion")
 */
class ThingRestController extends FOSRestController
{
    /**
     * /api/v1/thing/messages
     *
     * @RequestParam(name="message", requirements=".+", strict=true, allowBlank=false, description="Message to post")
     *
     * @param ParamFetcher $fetcher
     * @return Response
     *
     * @apiDescription 
     * Receives a message from a thing. Saves it to the thing's message list.
     * Message parameter should be appended to the POST body.
     * 
     * @api {post} /api/v1/thing/messages POST a message / status update to the thing's newsfeed
     * @apiGroup ThingRestController
     * @apiName PostMessage
     * @apiParam {String} message A field in the sent request. It contains the message to be saved. Is needed.
     *
     * @apiExample {curl} Example usage:
     *     curl --header "accesstoken: 12345" --data "message=mymessage" -i http://localhost/api/v1/thing/messages
     *
     * @apiHeader {String} accesstoken The thing's network access token. Used for athentication.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "accesstoken": "12345"
     *     }
     * 
     * @apiSuccess {Response} response The response to be sent. Contains the 200 response code and a short message.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": "200",
     *       "message": "Message updated"
     *     }
     * @apiVersion 0.1.0
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
     * @return Response
     *
     * @api {post} /api/v1/thing/functions/update Update thing-functions notification.
     * @apiGroup ThingRestController
     * @apiName PostThingFunctionsUpdate
     * @apiSuccess {Response} response Contains the 200 response code and a short message.
     * 
     * @apiDescription 
     * Receives an update notification from the thing and updates the saved functions with the current ones.
     * To get the updated data, a cURL call to the thing is made.
     *
     * @apiExample {curl} Example usage:
     *     curl --header "accesstoken: 12345" -i http://localhost/api/v1/thing/functions/update
     *
     * @apiHeader {String} accesstoken The thing's network access token. Used for athentication.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "accesstoken": "12345"
     *     }
     * 
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": "200",
     *       "message": "Functions updated"
     *     }
     * @apiVersion 0.1.0
     */
    public function postThingFunctionsUpdateAction(ParamFetcher $fetcher) {
        /** @var Thing $thing */
        $thing = $this->getUser();

        /** @var ThingManager $thingManager */
        $thingManager = $this->get('swot.manager.thing');
        $thingManager->removeFunctions($thing);

        /** @var CurlManager $curlManager */
        $curlManager = $this->get('services.curl_manager');
        /** @var ThingResponseConverter $converter */
        $converter = $this->get("thing_function_response_converter");

        $baseUrl = $thing->getBaseApiUrl();

        $functionsUrl = $baseUrl . $this->container->getParameter('thing.api.functions');
        $formattedUrl = URL::createFromUrl($functionsUrl);
        $functionsData = $curlManager->getCurlResponse($formattedUrl->__toString(), true, $thing->getReadToken());

        $functions = $converter->convertFunctions($functionsData);

        /** @var Action $function */
        foreach($functions as $function) {
            $thing->addFunction($function);
            $function->setThing($thing);
        }

        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($thing);

        $manager->flush();

        return $this->handleView(new View(array(
            "code" => Response::HTTP_OK,
            "message" => "Functions updated",
        )));
    }

    /**
     *
     * @RequestParam(name="information", strict=true, allowBlank=false, description="The thing's information")
     *
     * @Post("/information/update")
     *
     * @param ParamFetcher $fetcher
     * @return Response
     *
     * @api {post} /api/v1/thing/information/update Update thing-information notification.
     * @apiGroup ThingRestController
     * @apiName PostThingInformationUpdate
     * @apiParam {RequestParameter} information A field in the sent request.
     * @apiSuccess {Response} response The response to be sent. Contains the 200 response code and a short message.
     * @apiDescription 
     * Receives an update notification from the thing and updates the saved information with the current ones.
     * To get the updated data, a cURL call to the thing is made.
     *
     * @apiHeader {String} accesstoken The thing's network access token. Used for athentication.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "accesstoken": "12345"
     *     }
     *
     * @apiExample {curl} Example usage:
     *     curl --header "accesstoken: 12345" -i http://localhost/api/v1/thing/information/update
     * 
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": "200",
     *       "message": "Information updated"
     *     }
     * @apiVersion 0.1.0
     */
    public function postThingInformationUpdateAction(ParamFetcher $fetcher) {
        /** @var Thing $thing */
        $thing = $this->getUser();

        /** @var CurlManager $curlManager */
        $curlManager = $this->get('services.curl_manager');
        $informationData = $curlManager->getThingStatus($thing);

        $thing->setInformation($informationData);

        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($thing);

        $manager->flush();

        return $this->handleView(new View(array(
            "code" => Response::HTTP_OK,
            "message" => "Information updated",
        )));
    }

    /**
     *
     * @RequestParam(name="profileimage", strict=true, allowBlank=false, description="The thing's profile image")
     *
     * @Post("/profileimage/update")
     *
     * @param ParamFetcher $fetcher
     * @return Response
     *
     * @api {post} /api/v1/thing/profileimage/update Update thing profile image notification.
     * @apiGroup ThingRestController
     * @apiName PostThingProfileImageUpdate
     * @apiParam {String} profileimage Contains the url for the new profile image.
     * @apiSuccess {Response} response Contains the 200 response code and a short message.
     * @apiDescription 
     * Receives an update notification from the thing and updates the saved profile image with the current one.
     * To get the updated image, a cURL call to the thing is made.
     *
     * @apiHeader {String} accesstoken The thing's network access token. Used for athentication.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "accesstoken": "12345"
     *     }
     *
     * @apiExample {curl} Example usage:
     *     curl --header "accesstoken: 12345" --data "profileimage=UrlToUpdatedProfIleimage" -i http://localhost/api/v1/thing/profileimage/update
     * 
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": "200",
     *       "message": "ProfileImage updated"
     *     }
     * @apiVersion 0.1.0
     */
    public function postThingProfileImageUpdateAction(ParamFetcher $fetcher) {
        /** @var Thing $thing */
        $thing = $this->getUser();

        $imageUrl = $fetcher->get('profileimage');

        /** @var CurlManager $curlManager */
        $curlManager = $this->get('services.curl_manager');

        $formattedUrl = URL::createFromUrl($imageUrl);
        $profileImage = $curlManager->getCurlImageResponse($formattedUrl->__toString(), $thing->getReadToken());

        $thing->setProfileImage($profileImage);

        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($thing);

        $manager->flush();

        return $this->handleView(new View(array(
            "code" => Response::HTTP_OK,
            "message" => "ProfileImage updated",
        )));
    }

}
