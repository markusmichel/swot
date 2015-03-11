<?php

namespace Swot\NetworkBundle\Controller;

use Swot\NetworkBundle\Entity\Friendship;
use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Form\UserSettingsType;
use Swot\NetworkBundle\Security\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Shows the current user's profile.
     * Renders the same template as FriendController::show but without break up form.
     * @see Swot\NetworkBundle\Controller\FriendController::showAction
     * @return Response
     */
    public function profileAction() {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('SwotNetworkBundle:Friend:show.html.twig', array(
            'user'  => $user,
        ));
    }


    public function settingsAction(Request $request) {
        /** @var Translator $translator */
        $translator = $this->get('translator');

        /** @var User $user */
        $user = $this->getUser();
        $settingsForm = $this->createForm('user_settings', $user);

        $settingsForm->handleRequest($request);
        if($settingsForm->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $translator->trans('settings.save.success'));
        }

        return $this->render('SwotNetworkBundle:User:settings.html.twig', array(
            'settingsForm'  => $settingsForm->createView(),
        ));
    }

}
