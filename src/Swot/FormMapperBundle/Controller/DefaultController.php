<?php

namespace Swot\FormMapperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SwotFormMapperBundle:Default:index.html.twig', array('name' => $name));
    }
}
