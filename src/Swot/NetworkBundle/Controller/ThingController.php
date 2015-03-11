<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\Ownership;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Security\ThingVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        return $this->render("SwotNetworkBundle:Thing:show.html.twig", array(
            'delete_form'   => $deleteForm->createView(),
            'thing'         => $thing,
        ));
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
}
