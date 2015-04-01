<?php

namespace Swot\NetworkBundle\Services;


use Swot\FormMapperBundle\Entity\AbstractConstraint;
use Swot\FormMapperBundle\Entity\Action;
use Swot\FormMapperBundle\Entity\Parameter\Parameter;
use Swot\NetworkBundle\Entity\Thing;

class ThingResponseConverter {

    public function convertThing($thingInfo, $accessToken = null) {
        if($accessToken === null) $accessToken = uniqid();

        $thing = new Thing();
        $thing->setName($thingInfo->device->id);
        $thing->setNetworkAccessToken($accessToken);
        $thing->setBaseApiUrl($thingInfo->device->url);
        $thing->setOwnerToken($thingInfo->device->tokens->owner_token);
        $thing->setReadToken($thingInfo->device->tokens->read_token);
        $thing->setWriteToken($thingInfo->device->tokens->write_token);

        return $thing;
    }

    public function convertFunctions($functionsData) {
        $functions = array();

        foreach ($functionsData->functions as $func) {
            $function = new Action();
            $function->setName($func->name);
            $function->setUrl($func->url);

            $functions[] = $function;

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
                    }
                }

                $function->addParameter($parameter);
            }
        }

        return $functions;
    }

}