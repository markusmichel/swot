<?php

namespace Swot\NetworkBundle\Services;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("swot.security.network_token_generator")
 *
 * Class NetworkAccessTokenGenerator
 * @package Swot\NetworkBundle\Services
 */
class NetworkAccessTokenGenerator {

    public function generate() {
        return uniqid();
    }

}