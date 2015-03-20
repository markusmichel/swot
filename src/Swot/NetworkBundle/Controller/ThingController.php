<?php

namespace Swot\NetworkBundle\Controller;

use Swot\FormMapperBundle\Entity\NotBlank;
use Swot\FormMapperBundle\Entity\NotNull;
use Swot\FormMapperBundle\Entity\Parameter;
use Swot\FormMapperBundle\Entity\Action;
use Swot\NetworkBundle\Entity\Ownership;
use Swot\FormMapperBundle\Entity\AbstractConstraint;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Fixtures\ThingFixtures;
use Swot\NetworkBundle\Form\RentalType;
use Swot\FormMapperBundle\Form\FunctionType;
use Swot\NetworkBundle\Form\ThingType;
use Swot\NetworkBundle\Security\ThingVoter;
use Swot\NetworkBundle\Services\QrReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThingController extends Controller
{
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

        // Check if thing exists and has an owner
        if(null === $thing || $thing->getOwnership() === null) {
            // @todo: message string
            $this->addFlash('notice', 'Thing does not exist');
            return $this->redirectToRoute('my_things');
        }

        if (false === $this->get('security.authorization_checker')->isGranted(ThingVoter::ACCESS, $thing)) {
            $this->addFlash('notice', 'You are not allowed to see this thing');
            return $this->redirectToRoute('my_things');
        }

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

                die("valid");

                $accessToken = $thing->getAccessToken();
                $res = $function->activate($accessToken);

//                var_dump($res);
//                die();

                if(strcasecmp($res->status, "success") == 0) {
                    $this->addFlash('success', 'Function activated');
                    return $this->redirectToRoute('thing_show', array('id' => $thing->getId()));

                } else {
                    $this->addFlash('error', 'Function could not be activated');


                }
            }
        }

        return $this->render("SwotNetworkBundle:Thing:show.html.twig", array(
            'delete_form'   => $deleteForm->createView(),
            'thing'         => $thing,
            'status'        => $thingStatus,
            'functionForms' => $functionForms,
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

        if($thing === null) {
            $this->addFlash("error", "Thing does not exist");
            return $this->redirectToRoute('my_things');
        }

        if(!$this->isGranted(ThingVoter::ADMIN, $thing)) {
            $this->addFlash('error', 'Only a device\'s admin can access the settings page');
            $this->redirectToRoute('thing_show', array('id' => $id));
        }

        $form = $this->createForm(new ThingType(), $thing);
        $form->handleRequest($request);

        return $this->render('SwotNetworkBundle:Thing:settings.html.twig', array(
            'thing' => $thing,
            'form' => $form->createView(),
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

        if($thing === null) {
            $this->addFlash("error", "Thing does not exist");
            return $this->redirectToRoute('my_things');
        }

        if(!$this->isGranted(ThingVoter::ADMIN, $thing)) {
            $this->addFlash("error", "You may not lend this thing");
            return $this->redirectToRoute('my_things');
        }

        $rental = new Rental();
        $rental->setUserFrom($user);
        $rental->setAccessToken($thing->getAccessToken());
        $rental->setStarted(new \DateTime());
        $rental->setThing($thing);

        $form = $this->createForm('rental', $rental);

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

        /** @var QrReader $qrReader */
        $qrReader = $this->get('services.qr_reader');
        //@TODO get path from fileupload
        $qrContent = $qrReader->readQrCode("exampleQR.png");

        // @todo: remove fixture data
        $thing = new Thing();
        $thing->setName("Test Thing");
        $thing->setAccessToken("asdadasds");

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

    public function activateFunctionAction(Request $request, $id, $functionId) {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Thing $thing */
        $thing = $this->getDoctrine()->getRepository('SwotNetworkBundle:Thing')->find($id);

//        if($thing === null || !$this->isGranted('ACCESS', $thing)) {
//            $this->addFlash('error', 'You may not use this thing');
//            return $this->redirectToRoute('thing_show', array('id' => $id));
//        }

        /** @var Action $function */
        $function = $this->getDoctrine()->getRepository('SwotFormMapperBundle:Action')->find($functionId);
        if(!$thing->getFunctions()->contains($function)) {
            $this->addFlash('error', 'This function does not belong to the thing');
            return $this->redirectToRoute('thing_show', array('id' => $id));
        }

//        echo "<pre>";
//        print_r($request);
//        die();

        $function->activate($thing->getAccessToken());
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

        if($thing === null) {
            $this->addFlash('notice', 'Thing does not exist');
            return $this->redirectToRoute('my_things');
        }

        if (false === $this->get('security.authorization_checker')->isGranted(ThingVoter::ADMIN, $thing)) {
            $this->addFlash('notice', 'You are not allowed to delete this thing');
            return $this->redirectToRoute('my_things');
        }

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();

            /** @var Ownership $ownership */
            $ownership = $thing->getOwnership();
            $ownership->getOwner()->removeOwnership($ownership);

            /** @var Rental $rental */
            foreach($thing->getRentals() as $rental) {
                $rental->getThing()->removeRental($rental);
                $rental->getUserFrom()->removeThingsRent($rental);
                $rental->getUserFrom()->removeThingsLent($rental);
                $rental->getUserTo()->removeThingsRent($rental);
                $rental->getUserTo()->removeThingsLent($rental);

                $manager->persist($rental->getUserFrom());
                $manager->persist($rental->getUserTo());
                $manager->remove($rental);
            }

            $manager->remove($ownership);
            $manager->remove($thing);
            $manager->flush();
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
        $res = json_decode(ThingFixtures::$thingResponse);
        $functionsData = $res->device->functions;

        $manager = $this->getDoctrine()->getManager();

        foreach($functionsData as $func) {
            $function = new Action();
            $function->setThing($thing);
            $function->setName($func->name);
            $function->setUrl($func->url);

            foreach($func->parameters as $param) {
                $parameter = new Parameter();
                $parameter->setName($param->name);
                $parameter->setAction($function);
                $parameter->setType($param->type);

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
}
