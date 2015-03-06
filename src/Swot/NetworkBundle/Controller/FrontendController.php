<?php

namespace Swot\NetworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function newsfeedAction()
    {
        return $this->render('SwotNetworkBundle:Frontend:newsfeed.html.twig', array(
                // ...
            ));    }

}
