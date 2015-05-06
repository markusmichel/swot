<?php

namespace Swot\NetworkBundle\Tests\Service;


use League\Url\Url;
use Swot\NetworkBundle\Services\CurlManager;

class CurlManagerTest extends \PHPUnit_Framework_TestCase {

    private $manager;
    private $accessToken;

    public function setUp() {
        $this->manager = new CurlManager("", "/information");
        $this->accessToken = "123";
    }

    public function testThrowExceptionWithInvalidResponse() {

        $url = "http://www.sdkfhsdlkfjhsdlkjfhksdjfhksdjf.de";
        $formattedUrl = Url::createFromUrl($url);

        $hasException = false;

        try {
            $this->manager->getCurlResponse($formattedUrl->__toString(), true, "", $this->accessToken);
        } catch (\Exception $e) {
            $hasException = true;
        }

        $this->assertTrue($hasException, "Exception was not thrown with wrong data");
    }

    public function testCatchTryToGetPropertyOfNonObject() {
        $foo = array("ab", "b");

        $hasException = false;

        try {
            $foo->x;
        } catch(\Exception $e) {
            $hasException = true;
        }

        $this->assertTrue($hasException);
    }

}
