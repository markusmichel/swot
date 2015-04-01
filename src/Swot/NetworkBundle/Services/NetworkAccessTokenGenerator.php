<?php

namespace Swot\NetworkBundle\Services;

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