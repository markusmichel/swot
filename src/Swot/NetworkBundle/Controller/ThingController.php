<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\Rental;
use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThingController extends Controller
{

    public function showAction(Request $request, $id) {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Thing $thing */
        $thing = $this->getDoctrine()->getRepository("SwotNetworkBundle:Thing")->find($id);

        // Check if thing exists and has an owner
        if(null === $thing || $thing->getOwner() === null) {
            // @todo: message string
            $request->getSession()->getFlashBag()->add('notice', 'Thing does not exist');
            return $this->redirect($this->generateUrl('my_things'));
        }

        // Check if the thing is lent to the user
        $isThingLent = false;
        /** @var Rental $rental */
        foreach($thing->getRentals() as $rental) {
            if($user->getThingsLent()->contains($rental)) {
                $isThingLent = true;
                break;
            }
        }

        // Check if user has permission to show the thing
        // He has permission if he is the owner or the thing is lent to the user
        // @todo: check if the thing is public or restricted + owner is a friend
        if(!$thing->getOwner()->getOwner() === $this->getUser() && !$isThingLent) {
            $request->getSession()->getFlashBag()->add('notice', 'You are not allowed to see this thing');
            return $this->redirect($this->generateUrl('my_things'));
        }

        return $this->render("SwotNetworkBundle:Thing:show.html.twig", array(

        ));
    }

    public function deleteAction($id) {
        return new Response("delete " . $id);
    }

}
