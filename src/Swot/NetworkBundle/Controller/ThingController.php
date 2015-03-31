<?php

namespace Swot\NetworkBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Swot\FormMapperBundle\Entity\NotBlank;
use Swot\FormMapperBundle\Entity\NotNull;
use Swot\FormMapperBundle\Entity\Parameter\Parameter;
use Swot\FormMapperBundle\Entity\Action;
use Swot\NetworkBundle\Entity\Ownership;
use Swot\FormMapperBundle\Entity\AbstractConstraint;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Exception\AccessToThingDeniedException;
use Swot\NetworkBundle\Exception\ThingNotFoundException;
use Swot\NetworkBundle\Fixtures\ThingFixtures;
use Swot\NetworkBundle\Form\RentalType;
use Swot\FormMapperBundle\Form\FunctionType;
use Swot\NetworkBundle\Form\ThingType;
use Swot\NetworkBundle\Security\ThingVoter;
use Swot\NetworkBundle\Services\CurlManager;
use Swot\NetworkBundle\Services\QrReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThingController extends Controller
{

    /**
     * @param $thing Thing
     * @throws ThingNotFoundException
     */
    protected function assertThingExists($thing) {
        // Check if thing exists and has an owner
        if(null === $thing || $thing->getOwnership() === null) {
            throw new ThingNotFoundException("Thing does not exist");
        }
    }

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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function showAction(Request $request, $id) {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Thing $thing */
        $thing = $this->getDoctrine()->getRepository("SwotNetworkBundle:Thing")->find($id);
        $this->assertThingExists($thing);
        $this->assertAccessToThingGranted($thing, ThingVoter::ACCESS);

        $deleteForm = $this->createDeleteForm($id);
        $thingStatus = json_decode(ThingFixtures::$thingResponse);

        $functionForms = $this->createActivateFunctionForms($thing);

        // Check if ONE form was submitted and which was it was.
        // Validate the form and activate the function on the thing if valid.
        // @todo: check if admin function
        if($request->getMethod() === "POST" && $request->request->has('_fid') && $this->isGranted(ThingVoter::ACCESS, $thing)) {
            $fid = $request->request->get('_fid');

            /** @var Form $form */
            $form = $functionForms[$fid];

            /** @var Action $function */
            $function = $this->getDoctrine()->getRepository('SwotFormMapperBundle:Action')->find($fid);

            $form->handleRequest($request);
            if($form->isValid() === true) {

                $accessToken = $thing->getNetworkAccessToken();
                $res = $function->activate($accessToken);

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
     * Shows a thing's settings.
     * Only accessible by the thing's owner.
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function settingsAction(Request $request, $id) {
        /** @var Thing $thing */
        $thing = $this->getDoctrine()->getRepository("SwotNetworkBundle:Thing")->find($id);

        $this->assertThingExists($thing);
        $this->assertAccessToThingGranted($thing, ThingVoter::ADMIN);

        $form = $this->createForm(new ThingType(), $thing);
        $form->handleRequest($request);

        $rentals = $this->getDoctrine()->getRepository("SwotNetworkBundle:Rental")->findActiveRentals($thing);

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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function lendAction(Request $request, $id) {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Thing $thing */
        $thing = $this->getDoctrine()->getRepository("SwotNetworkBundle:Thing")->find($id);

        $this->assertThingExists($thing);
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

        if($useQR == 1){
            $data = array();
            $form = $this->createFormBuilder($data)
                ->add('register','file')
                ->getForm();

            if ($request->isMethod('POST')) {
                $form->handleRequest($request);

                if ($form->isValid()) {

                    $data = $form->getData();
                    /** @var UploadedFile $file */
                    $file = $data['register'];
                    $qr = $file->move($file->getPath(),"qr.png");
                    $url = $this->getUrlFromQr($qr);

                    /** @var CurlManager $curlManager */
                    $curlManager = $this->get('services.curl_manager');

                    // accesstoken for the thing to communicate with the network
                    $accessToken = uniqid();
                    //@TODO: better way to add parameters?
                    $thingInfo = $curlManager->getCurlResponse($url . "&network_token=" . $accessToken);

                    $thingName = $thingInfo->device->id;
                    $functionsUrl = $thingInfo->device->api->function .  "?access_token=" . $thingInfo->device->tokens->owner;
                    $functionsData = $curlManager->getCurlResponse($functionsUrl);

                    $manager = $this->getDoctrine()->getManager();

                    $thing = new Thing();
                    $thing->setName($thingName);
                    $thing->setNetworkAccessToken($accessToken);

                    $ownership = new Ownership();
                    $ownership->setThing($thing);
                    $ownership->setOwner($user);
                    $user->addOwnership($ownership);
                    $thing->setOwnership($ownership);

                    $this->generateThingData($functionsData, $thing, $manager);

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($ownership);
                    $manager->persist($user);
                    $manager->persist($thing);

                    $manager->flush();

                    $this->addFlash("success", "Thing added");
                    return $this->redirectToRoute('my_things');
                }else{
                    $this->addFlash("failure", "Nothing added");
                    return $this->redirectToRoute('my_things');
                }
            }

        } else {
            // @todo: remove fixture data
            $thing = new Thing();
            $thing->setName("Test Thing");
            $thing->setNetworkAccessToken("asdadasds");

            $ownership = new Ownership();
            $ownership->setThing($thing);
            $ownership->setOwner($user);
            $user->addOwnership($ownership);
            $thing->setOwnership($ownership);

            $this->generateTestFunction($thing);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ownership);
            $manager->persist($user);
            $manager->persist($thing);

            $manager->flush();

            $this->addFlash("success", "Thing added");
            return $this->redirectToRoute('my_things');
        }
    }

    /**
     * Completely delete a thing.
     * Removes it from the database, from the owner and all included rentals.
     * Calls /deregister on the real related thing.
     * @see createDeleteForm
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id) {
        /** @var Thing $thing */
        $thing = $this->getDoctrine()->getRepository('SwotNetworkBundle:Thing')->find($id);

        $this->assertThingExists($thing);
        $this->assertAccessToThingGranted($thing, ThingVoter::ADMIN);

        $form = $this->createDeleteForm($id);
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
     * @param $thing Thing
     * @return array
     */
    private function generateTestFunction($thing)
    {
        //@TODO: only for development --> delete
        $res = json_decode(ThingFixtures::$thingResponse);
        $functionsData = $res->device->functions;

        $manager = $this->getDoctrine()->getManager();

        foreach($functionsData as $func) {
            $function = new Action();
            $function->setThing($thing);
            $function->setName($func->name);
            $function->setUrl($func->url);

            foreach($func->parameters as $param) {
                $parameter = Parameter::createParameter($param);
                $parameter->setAction($function);

                if(isset($param->constraints)) {
                    foreach($param->constraints as $con) {
                        $className = "\\Swot\\FormMapperBundle\\Entity\\" . $con->type;
                        if(!class_exists($className)) continue;

                        /** @var AbstractConstraint $constraint */
                        $constraint = new $className;
                        $constraint->init($con);
                        $constraint->setMessage($con->message);
                        $constraint->setFunctionParameter($parameter);

                        $parameter->addConstraint($constraint);
                        $manager->persist($constraint);
                    }
                }

                $function->addParameter($parameter);
                $manager->persist($parameter);
            }

            $thing->addFunction($function);
            $manager->persist($function);
            $manager->persist($thing);
        }

        $manager->flush();
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

    /**
     * Generates the data of the related thing.
     *
     * @param $functionsData String contains the functions to be added to the thing
     * @param $thing Thing new thing
     * @param $manager Object the entity manger
     */
    private function generateThingData($functionsData, $thing, $manager)
    {
        foreach ($functionsData->functions as $func) {
            $function = new Action();
            $function->setThing($thing);
            $function->setName($func->name);
            $function->setUrl($func->url);

            foreach ($func->parameters as $param) {
                $parameter = Parameter::createParameter($param);
                $parameter->setAction($function);

                if (isset($param->constraints)) {
                    foreach ($param->constraints as $con) {
                        $className = "\\Swot\\FormMapperBundle\\Entity\\" . $con->type;
                        if (!class_exists($className)) continue;

                        /** @var AbstractConstraint $constraint */
                        $constraint = new $className;
                        $constraint->init($con);
                        $constraint->setMessage($con->message);
                        $constraint->setFunctionParameter($parameter);

                        $parameter->addConstraint($constraint);
                        $manager->persist($constraint);
                    }
                }

                $function->addParameter($parameter);
                $manager->persist($parameter);
            }

            $thing->addFunction($function);
            $manager->persist($function);
            $manager->persist($thing);
        }

        $manager->flush();
    }

}
