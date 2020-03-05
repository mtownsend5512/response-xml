<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Routing\ResponseFactory as Response;
use Mtownsend\ResponseXml\Providers\ResponseXmlServiceProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass as Reflect;

class ResponseXml extends TestCase
{

    /** @test array */
    protected $testArray = [];

    /** @test string */
    protected $testXml;

    public function setUp() : void
    {
        $this->createDummyprovider()->register();

        $this->testArray = [
            'carrier' => 'fedex',
            'id' => 123,
            'tracking_number' => '9205590164917312751089'
        ];
        $this->testXml = '<?xml version="1.0"?><response><carrier>fedex</carrier><id>123</id><tracking_number>9205590164917312751089</tracking_number></response>';
    }

    /**
     * Create a mock request
     *
     * @return Illuminate\Http\Request
     */
    public function createDummyResponse($xml = null, $status = 200, $headers = [], $xmlRoot = 'response'): Response
    {
        $reflectionClass = new Reflect(Response::class);
        return $reflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * Create a mock request
     *
     * @return Illuminate\Http\Request
     */
    public function createDummyRequest($headers = [], $payload = null): Request
    {
        return new Request([], [], [], [], [], $headers, $payload);
    }

    /**
     * Create a dummy IOC container
     *
     * @return Illuminate\Foundation\Application
     */
    public function createDummyContainer(): Application
    {
        return new Illuminate\Foundation\Application(
            realpath(__DIR__.'/../')
        );
    }

    /**
     * Bootstrap the provider
     *
     */
    protected function createDummyprovider(): ResponseXmlServiceProvider
    {
        $reflectionClass = new Reflect(ResponseXmlServiceProvider::class);
        return $reflectionClass->newInstanceWithoutConstructor();
    }

    /**
     * Remove new lines from xml to standardize testing
     *
     */
    protected function removeNewLines($string)
    {
        return preg_replace('~[\r\n]+~', '', $string);
    }

    /** @test */
    public function response_sends_string_xml()
    {
        $response = $this->createDummyResponse()->xml($this->testXml, 200);
        $this->assertEquals($this->testXml, $response->original);
    }

    /** @test */
    public function response_sends_array_converted_to_xml()
    {
        $response = $this->createDummyResponse()->xml($this->testArray, 200);
        $this->assertEquals($this->testXml, $this->removeNewLines($response->getContent()));
    }

    /** @test */
    public function response_sends_collection_converted_to_xml()
    {
        $response = $this->createDummyResponse()->xml(new Collection($this->testArray), 200);
        $this->assertEquals($this->testXml, $this->removeNewLines($response->getContent()));
    }

    /** @test */
    public function response_allows_custom_headers()
    {
        $response = $this->createDummyResponse()->xml($this->testArray, 200, ['Foo' => 'Bar']);
        $this->assertEquals('Bar', $response->headers->get('Foo'));
    }

    /** @test */
    public function response_auto_adds_content_type_header_if_unset()
    {
        $response = $this->createDummyResponse()->xml($this->testArray, 200);
        $this->assertEquals('application/xml', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function response_allows_content_type_override()
    {
        $response = $this->createDummyResponse()->xml($this->testArray, 200, ['Content-Type' => 'text/xml']);
        $this->assertEquals('text/xml', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function response_preferred_format_returns_xml()
    {
        $request = $this->createDummyRequest([], $this->testXml);
        $request->headers->set('Accept', 'application/xml');
        $container = $this->createDummyContainer();
        $container->instance('request', $request);
        $response = $this->createDummyResponse()->preferredFormat($this->testArray, 200);
        $this->assertEquals($this->testXml, $this->removeNewLines($response->getContent()));
    }

    /** @test */
    public function response_preferred_format_returns_xml_and_proper_content_type_header()
    {
        $request = $this->createDummyRequest([], $this->testXml);
        $request->headers->set('Accept', 'application/xml');
        $container = $this->createDummyContainer();
        $container->instance('request', $request);
        $response = $this->createDummyResponse()->preferredFormat($this->testArray, 200);
        $this->assertEquals('application/xml', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function response_preferred_format_returns_json()
    {
        $request = $this->createDummyRequest([], json_encode($this->testArray));
        $request->headers->set('Accept', 'application/json');
        $container = $this->createDummyContainer();
        $container->instance('request', $request);
        $response = $this->createDummyResponse()->preferredFormat($this->testArray, 200);
        $this->assertEquals(json_encode($this->testArray), $response->getContent());
    }

    /** @test */
    public function response_preferred_format_returns_json_and_proper_content_type_header()
    {
        $request = $this->createDummyRequest([], json_encode($this->testArray));
        $request->headers->set('Accept', 'application/json');
        $container = $this->createDummyContainer();
        $container->instance('request', $request);
        $response = $this->createDummyResponse()->preferredFormat($this->testArray, 200);
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }
}
