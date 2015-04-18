<?php

namespace Swot\NetworkBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swot\FormMapperBundle\Entity\Action;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Exception\AccessToThingDeniedException;
use Swot\NetworkBundle\Fixtures\ThingFixtures;
use Swot\FormMapperBundle\Form\FunctionType;
use Swot\NetworkBundle\Form\ThingType;
use Swot\NetworkBundle\Security\ThingVoter;
use Swot\NetworkBundle\Services\CurlManager;
use Swot\NetworkBundle\Services\Manager\ThingManager;
use Swot\NetworkBundle\Services\QrReader;
use Swot\NetworkBundle\Services\ThingResponseConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Url\Url;

class ThingController extends Controller
{
    /**
     * @param $thing Thing
     * @param $accessType
     */
    protected function assertAccessToThingGranted($thing, $accessType) {
        if (false === $this->get('security.authorization_checker')->isGranted($accessType, $thing)) {
            throw new AccessToThingDeniedException();
        }
    }

    /**
     * Shows a thing's profile.
     * @param Request $request
     * @ParamConverter("thing", class="SwotNetworkBundle:Thing")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function showAction(Request $request, Thing $thing) {
        /** @var User $user */
        $user = $this->getUser();

        $this->assertAccessToThingGranted($thing, ThingVoter::ACCESS);

        $deleteForm = $this->createDeleteForm($thing->getId());
        //@TODO: implement status handling
        $thingStatus = json_decode(ThingFixtures::$thingResponse);

        $functionForms = $this->createActivateFunctionForms($thing);

        // Check if ONE form was submitted and which was it was.
        // Validate the form and activate the function on the thing if valid.
        // @todo: check if admin function#
        if($request->getMethod() === "POST" && $request->request->has('_fid') && $this->isGranted(ThingVoter::ACCESS, $thing)) {
            $fid = $request->request->get('_fid');

            /** @var Form $form */
            $form = $functionForms[$fid];

            /** @var Action $function */
            $function = $this->getDoctrine()->getRepository('SwotFormMapperBundle:Action')->find($fid);

            $form->handleRequest($request);
            if($form->isValid() === true) {

                $accessToken = null;
                if($this->isGranted(ThingVoter::ACCESS, $thing))
                    $accessToken = $thing->getWriteToken();
                else if ($this->isGranted(ThingVoter::ADMIN, $thing))
                    $accessToken = $thing->getOwnerToken();

                $res = $function->activate($accessToken);

                // @todo: validate response code instead of json message
                if(strcasecmp($res->status, "success") == 0) {
                    $this->addFlash('success', 'Function activated');
                    return $this->redirectToRoute('thing_show', array('id' => $thing->getId()));

                } else {
                    $this->addFlash('error', 'Function could not be activated');
                }
            }
        }

        $messages = $this->getDoctrine()->getRepository('SwotNetworkBundle:ThingStatusUpdate')->findBy(array(
            'thing' => $thing,
        ), array(
            'sent' => 'DESC',
        ));

        return $this->render("SwotNetworkBundle:Thing:show.html.twig", array(
            'delete_form'   => $deleteForm->createView(),
            'thing'         => $thing,
            'status'        => $thingStatus,
            'functionForms' => $functionForms,
            'messages'      => $messages,
        ));
    }

    /**
     * Shows messages of a thing since a date
     *
     * @ParamConverter("thing", class="SwotNetworkBundle:Thing")
     * @param Thing $thing
     * @return Response
     */
    public function showUpdatesSinceAction(Thing $thing, $since, $_format) {
        $this->assertAccessToThingGranted($thing, ThingVoter::ACCESS);

        $sinceDate = new \DateTime();
        $sinceDate->setTimestamp(intval($since));
        $updates = $this->getDoctrine()->getRepository("SwotNetworkBundle:ThingStatusUpdate")->findUpdatesForThingSince($thing, $sinceDate);

        switch($_format) {
            case "html":
                return new Response($this->renderView("SwotNetworkBundle:Thing:thing_messages.html.twig", array("messages" => $updates)));
                break;
            case "json":
                $serializer = $this->container->get('jms_serializer');
                $serialized = $serializer->serialize($updates, "json");

                $response = new Response($serialized);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
                break;
        }
    }

    /**
     * Shows a thing´s information
     *
     * @ParamConverter("thing", class="SwotNetworkBundle:Thing")
     * @param Thing $thing
     * @return Response
     */
    public function showInformationAction(Thing $thing) {
        $this->assertAccessToThingGranted($thing, ThingVoter::ACCESS);

        $information = $thing->getInformation();
        $information = json_decode(ThingFixtures::$informationResponse);

        $information->status[3]->value = rand(0, 100);

        return new JsonResponse($information);
    }

    /**
     * Shows a thing's settings.
     * Only accessible by the thing's owner.
     *
     * @param Request $request
     * @ParamConverter("thing", class="SwotNetworkBundle:Thing")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function settingsAction(Request $request, Thing $thing) {
        $this->assertAccessToThingGranted($thing, ThingVoter::ADMIN);

        $form = $this->createForm(new ThingType(), $thing);
        $form->handleRequest($request);

        $rentals = $this->getDoctrine()->getRepository("SwotNetworkBundle:Rental")->findActiveRentals($thing);

        if(true === $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($thing);
            $manager->flush();
            $this->addFlash("success", "Settings saved.");
        }

        return $this->render('SwotNetworkBundle:Thing:settings.html.twig', array(
            'thing' => $thing,
            'form' => $form->createView(),
            'rentals' => $rentals,
        ));
    }

    /**
     * Lend a thing to another user.
     * Only the owner can lend things.
     *
     * @param Request $request
     * @ParamConverter("thing", class="SwotNetworkBundle:Thing")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function lendAction(Request $request, Thing $thing) {
        /** @var User $user */
        $user = $this->getUser();
        $this->assertAccessToThingGranted($thing, ThingVoter::ADMIN);

        $rental = new Rental();
        $rental->setUserFrom($user);
        $rental->setAccessToken($thing->getNetworkAccessToken());
        $rental->setStarted(new \DateTime());
        $rental->setThing($thing);

        $form = $this->createForm('rental', $rental);
        $form->handleRequest($request);

        if($form->isValid()) {
            $user->addThingsLent($rental);
            $rental->getUserTo()->addThingsRent($rental);
            $this->getDoctrine()->getManager()->persist($rental);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->persist($rental->getUserTo());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", sprintf("Thing %s lent to %s", $thing->getName(), $rental->getUserTo()->getUsername()));
            return $this->redirectToRoute('thing_show', array("id" => $thing->getId()));
        }

        return $this->render('SwotNetworkBundle:Thing:lend.html.twig', array(
            'form' => $form->createView(),
            'thing' => $thing,
        ));
    }

    /**
     * Registers a new thing to the network/user.
     * The thing itself should throw an error if it is not available for registration.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function registerAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        //@TODO $useQR only for development
        $useQR = 0;

        $data = array();
        $form = $this->createFormBuilder($data)
            ->add('register','file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            // accesstoken for the thing to communicate with the network
            $accessToken = $this->container->get("swot.security.network_token_generator")->generate();

            $data = $form->getData();

            /** @var UploadedFile $file */
            $file = $data['register'];
            $qr = $file->move($file->getPath(),"qr.png");

            // dev switch
            $functionsData = null;
            $profileImage = null;
            if($useQR == 1) {
                $url = $this->getUrlFromQr($qr);

                /** @var CurlManager $curlManager */
                $curlManager = $this->get('services.curl_manager');

                $formattedUrl = URL::createFromUrl($url);
                $query = $formattedUrl->getQuery();
                $query["network_token"] = $accessToken;
                $formattedUrl->setQuery($query);
                $thingInfo = $curlManager->getCurlResponse($formattedUrl->__toString(), true);

                $imageUrl = URL::createFromUrl($thingInfo->device->api->profileimage);
                //@TODO: keep without tokens?!
                $profileImage = $curlManager->getCurlImageResponse($imageUrl->__toString());

                $functionsUrl = URL::createFromUrl($thingInfo->device->api->function);
                $query = $functionsUrl->getQuery();
                $query["access_token"] = $thingInfo->device->tokens->owner_token;
                $functionsUrl->setQuery($query);
                $functionsData = $curlManager->getCurlResponse($functionsUrl->__toString(), true);

            } else {
                $res = json_decode(ThingFixtures::$thingResponse);
                $thingInfo = $res;
                $functionsData = $res->device;
            }

            /** @var ThingResponseConverter $converter */
            $converter = $this->get("thing_function_response_converter");

            /** @var ThingManager $thingManager */
            $thingManager = $this->container->get("swot.manager.thing");

            // Create thing from response
            $thing = $converter->convertThing($thingInfo, $profileImage, $accessToken);

            $ownership = $thingManager->createOwnership($thing, $user);

            // Convert response to Action/Function objects.
            // Add them to the thing.
            $functions = $converter->convertFunctions($functionsData);

            /** @var Action $function */
            foreach($functions as $function) {
                $thing->addFunction($function);
                $function->setThing($thing);
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ownership);
            $manager->persist($user);
            $manager->persist($thing);

            $manager->flush();
        }
        // Invalid
        else {
            $this->addFlash("failure", "Nothing added");
            return $this->redirectToRoute('my_things');
        }

        $this->addFlash("success", "Thing added");
        return $this->redirectToRoute('my_things');
    }

    /**
     * Completely delete a thing.
     * Removes it from the database, from the owner and all included rentals.
     * Calls /deregister on the real related thing.
     * @see createDeleteForm
     * @param Request $request
     * @ParamConverter("thing", class="SwotNetworkBundle:Thing")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Thing $thing) {
        $this->assertAccessToThingGranted($thing, ThingVoter::ADMIN);

        $form = $this->createDeleteForm($thing->getId());
        $form->handleRequest($request);

        if($form->isValid()) {
            // remove thing from database
            // @see: \Swot\NetworkBundle\Services\Manager\ThingManager
            $this->get("swot.manager.thing")->remove($thing);
        }

        return $this->redirectToRoute('my_things');
    }

    /**
     * Creates a delete form to delete a thing.
     * @see deleteAction
     * @param $id
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('thing_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Creates one ActivationForm for every function of the thing.
     * Array indexes are the functions' ids.
     *
     * @param Thing $thing
     * @return array
     */
    private function createActivateFunctionForms(Thing $thing) {
        $forms = array();

        /** @var Action $func */
        foreach($thing->getFunctions() as $func) {
            $forms[$func->getId()] = $this->createActivateFunctionForm($func);
        }

        return $forms;
    }

    /**
     * Create an ActiviationForm containing the function's parameters.
     *
     * @param Action $function
     * @return \Symfony\Component\Form\Form
     */
    private function createActivateFunctionForm(Action $function) {
        $form = $this->createForm(new FunctionType(), $function);
        return $form;
    }

    /**
     * Encodes a qr code and return its value
     * @param $qr the QrFile to encode
     * @return mixed The registration url of the thing
     */
    private function getUrlFromQr($qr){
        /** @var QrReader $qrReader */
        $qrReader = $this->get('services.qr_reader');
        $qrContent = json_decode($qrReader->readQrCode($qr->getPathname()));
        $url = $qrContent->url;
        return $url;
    }
}
