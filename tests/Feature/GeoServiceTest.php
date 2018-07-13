<?php

namespace Tests\Feature;

use App\GeoException;
use App\GeoService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeoServiceTest extends TestCase
{
    private $validApiKey;
    private $wrongApiKey;

    public function setUp()
    {
        parent::setUp();
        $this->createApplication();
        $this->validApiKey = new GeoService();
        $this->wrongApiKey = new GeoService("definitely_not_an_api_key");
    }

    /**
     *
     *
     * @return void
     */
    public function testValidApiKeyAndRealRequest()
    {
        /** @var GeoService $service */
        $service = $this->validApiKey;
        try {
            $result = $service->get("Moscow");
            $this->assertNotEmpty($result);
            $this->assertFalse($service->hasErrors());
            $this->assertEquals('HTTP: Request completed | RESULT: Ok | COUNT: 5 | ERROR MESSAGE: ', $service->status());
        } catch (GeoException $e) {
            $this->fail(sprintf("Got exception while executing request: %s",
                $e->getMessage()
            ));
        }
        $this->assertTrue(true);
    }

    /**
     *
     *
     * @return void
     */
    public function testValidApiKeyAndInvalidRequest()
    {
        /** @var GeoService $service */
        $service = $this->validApiKey;
        try {
            $result = $service->get("Almsivi");

            $this->assertEmpty($result);
            $this->assertFalse($service->hasErrors());
            $this->assertEquals('HTTP: Request completed | RESULT: Zero results | COUNT: 0 | ERROR MESSAGE: ', $service->status());
        } catch (GeoException $e) {
            $this->fail(sprintf("Got exception while executing request: %s",
                $e->getMessage()
            ));
        }
        $this->assertTrue(true);
    }

    /**
     *
     *
     * @return void
     */
    public function testWrongApiKeyAndValidRequest()
    {
        /** @var GeoService $service */
        $service = $this->wrongApiKey;
        try {
            $result = $service->get("Vivec");

            $this->assertEmpty($result);
            $this->assertTrue($service->hasErrors());
            $this->assertEquals('HTTP: Request completed | RESULT: Request denied | COUNT: 0 | ERROR MESSAGE: The provided API key is invalid.'
                , $service->status());
        } catch (GeoException $e) {
            $this->fail(sprintf("Got exception while executing request: %s",
                $e->getMessage()
            ));
        }
        $this->assertTrue(true);
    }

}
