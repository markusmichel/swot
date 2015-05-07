<?php

namespace Swot\NetworkBundle\Services;


use Swot\NetworkBundle\Exception\ThingAlreadyRegisteredException;
use Swot\NetworkBundle\Exception\ThingIsUnavailableException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


class ExceptionListener {

    private $twig;

    public function __construct(\Twig_Environment $twig) {
        $this->twig = $twig;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();

        // Customize your response object to display the exception details
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/html');

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof ThingIsUnavailableException) {
            $content = $this->twig->render(':Exception:thing_is_unavailable.html.twig');
            $response->setContent($content);

            // Send the modified response object to the event
            $event->setResponse($response);

        } elseif ($exception instanceof ThingAlreadyRegisteredException) {
            $content = $this->twig->render(':Exception:thing_already_registered.html.twig');
            $response->setContent($content);

            // Send the modified response object to the event
            $event->setResponse($response);
        }
    }
}