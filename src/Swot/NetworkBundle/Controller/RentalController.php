<?php

namespace Swot\NetworkBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Security\RentalVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RentalController extends Controller
{
    private function getThing($id) {
        $thing = $this->getDoctrine()->getRepository("SwotNetworkBundle:Thing")->find($id);
        if($thing === null) throw $this->createAccessDeniedException();

        return $thing;
    }

    /**
     * @param Request $request
     * @param $thingid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, $thingid) {
        $thing = $this->getThing($thingid);
        $rentals = $this->getDoctrine()->getRepository("SwotNetworkBundle:Rental")->findActiveRentals($thing);

        $deleteForms = $this->createQuitRentalsForms($rentals);

        return $this->render("SwotNetworkBundle:Rental:list.html.twig", array(
            "rentals" => $rentals,
            "deleteForms" => $deleteForms,
        ));
    }

    /**
     * Ends a rental.
     * Only users who have lent OR rent the thing may quit the rental.
     *
     * @param Request $request
     * @param $thingid
     * @param $rentalid
     * @return JsonResponse
     * @throws EntityNotFoundException
     * @internal param int $id id of the rental
     */
    public function quitAction(Request $request, $thingid, $rentalid) {
        // currently only supports AjaxRequests
        if(!$request->isXmlHttpRequest()) throw $this->createAccessDeniedException();

        /** @var Rental $rental */
        $rental = $this->getDoctrine()->getRepository("SwotNetworkBundle:Rental")->find($rentalid);

        /** @var User $user */
        $user = $this->getUser();

        if($rental === null) throw new EntityNotFoundException();

        if(false === $this->isGranted(RentalVoter::QUIT, $rental)) throw $this->createAccessDeniedException();

        $user->removeThingsRent($rental);
        $user->removeThingsLent($rental);

        $otherUser = $rental->getOtherUser($user);
        $otherUser->removeThingsRent($rental);
        $otherUser->removeThingsLent($rental);

        $rental->setUserFrom(null);
        $rental->setUserTo(null);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->persist($otherUser);
        $manager->remove($rental);
        $manager->flush();

        return new JsonResponse(array("Rental removed"), 200);
    }

    /**
     * Internal action without route.
     *
     * Zeigt die dem Nutzer geliehenen Dinge.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showThingsLentAction() {
        $thingsLent = $this->getDoctrine()->getRepository("SwotNetworkBundle:Rental")->findActiveRentThingsByUser($this->getUser());

        return $this->render("SwotNetworkBundle:Rental:_things_rent.html.twig", array(
            "things" => $thingsLent,
        ));
    }

    /**
     * Internal action without route.
     *
     * Zeigt die vom Nutzer verliehenen Dinge.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showThingsRentAction() {
        $thingsLent = $this->getDoctrine()->getRepository("SwotNetworkBundle:Rental")->findActiveLentThingsByUser($this->getUser());

        return $this->render("SwotNetworkBundle:Rental:_things_lent.html.twig", array(
            "things" => $thingsLent,
        ));
    }



    private function createQuitRentalsForms($rentals) {
        $forms = array();

        /** @var Rental $rental */
        foreach($rentals as $rental) {
            $forms[$rental->getId()] = $this->createQuitRentalForm($rental)->createView();
        }

        return $forms;
    }

    /**
     * @param Rental $rental
     * @return \Symfony\Component\Form\Form
     */
    private function createQuitRentalForm(Rental $rental) {
        $form = $this->createFormBuilder($rental)
            ->setAction($this->generateUrl('rental_quit', array(
                "thingid" => $rental->getThing()->getId(),
                "rentalid" => $rental->getId(),
            )))
            ->setMethod('DELETE')
            ->add('quit', 'submit');

        return $form->getForm();
    }
}
