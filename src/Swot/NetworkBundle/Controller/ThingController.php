<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\FunctionParameter;
use Swot\NetworkBundle\Entity\Ownership;
use Swot\NetworkBundle\Entity\ParameterConstraint;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\ThingFunction;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Fixtures\ThingFixtures;
use Swot\NetworkBundle\Form\ThingFunctionType;
use Swot\NetworkBundle\Security\ThingVoter;
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

        // @todo: check if user may use this thing
        $thingStatus = json_decode(ThingFixtures::$thingResponse);

        $functionForms = $this->createActivateFunctionForms($thing);

        if($request->getMethod() === "POST" && $request->request->has('_fid')) {
            $fid = $request->request->get('_fid');

            /** @var Form $form */
            $form = $functionForms[$fid];

            $form->handleRequest($request);
            if($form->isValid() === true) {
                // @todo: activate thing function
                $this->addFlash('success', 'Function activated');
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
     * Registers a new thing to the network/user.
     * The thing itself should throw an error if it is not available for registration.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function registerAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        // @todo: remove fixture data
        $thing = new Thing();
        $thing->setName("Test Thing");
        $thing->setAccessToken("asdadasds");

        $ownership = new Ownership();
        $ownership->setThing($thing);
        $ownership->setOwner($user);
        $user->addOwnership($ownership);
        $thing->setOwnership($ownership);

        list($func, $param, $param2, $param3, $constraint) = $this->generateTestFunction($thing);
        list($func2, $param4, $param5, $param6, $constraint2) = $this->generateTestFunction($thing);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($ownership);
        $manager->persist($user);
        $manager->persist($thing);
        $manager->persist($func);
        $manager->persist($param);
        $manager->persist($param2);
        $manager->persist($param3);
        $manager->persist($constraint);

        $manager->persist($func2);
        $manager->persist($param4);
        $manager->persist($param5);
        $manager->persist($param6);
        $manager->persist($constraint2);



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

        echo "<pre>";
        print_r($request);
        die();

        $thingStatus = json_decode(ThingFixtures::$thingResponse);
        $functionUrl = "";
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

        /** @var ThingFunction $func */
        foreach($thing->getFunctions() as $func) {
            $forms[$func->getId()] = $this->createActivateFunctionForm($func);
        }

        return $forms;
    }

    /**
     * Create an ActiviationForm containing the function's parameters.
     *
     * @param ThingFunction $function
     * @return \Symfony\Component\Form\Form
     */
    private function createActivateFunctionForm(ThingFunction $function) {
        $form = $this->createForm(new ThingFunctionType(), $function);
        return $form;
    }

    /**
     * @param $thing
     * @return array
     */
    private function generateTestFunction($thing)
    {
        $func = new ThingFunction();
        $func->setThing($thing);
        $func->setName("Set temperature");
        $func->setUrl("http://www.example.com");

        $param = new FunctionParameter();
        $param->setName("temperature");
        $param->setThingFunction($func);
        $param->setType("integer");

        $param2 = new FunctionParameter();
        $param2->setName("temperature-2");
        $param2->setThingFunction($func);
        $param2->setType("integer");

        $param3 = new FunctionParameter();
        $param3->setName("temperature-3");
        $param3->setThingFunction($func);
        $param3->setType("integer");

        $constraint = new ParameterConstraint();
        $constraint->setType("NotNull");
        $constraint->setFunctionParameter($param);
        $constraint->setMessage("Temperature may not be empty");

        $thing->addFunction($func);
        $func->addParameter($param);
        $func->addParameter($param2);
        $func->addParameter($param3);
        $param->addConstraint($constraint);
        $param2->addConstraint($constraint);
        $param2->addConstraint($constraint);
        return array($func, $param, $param2, $param3, $constraint);
    }
}
