<?php

namespace TheIconic\Tracking\GoogleAnalytics;

use GuzzleHttp\Message\Uri;
use GuzzleHttp\Message\RequestInterface;

class AnalyticsResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnalyticsResponse
     */
    private $analyticsResponse;

    /**
     * @var AnalyticsResponse
     */
    private $analyticsResponseAsync;

    /**
     * @var AnalyticsResponse
     */
    private $analyticsDebugResponse;

    /**
     * @var RequestInterface
     */
    private $mockRequest;

    public function setUp()
    {
        $mockResponse = $this->getMockBuilder('GuzzleHttp\Message\Response')
            ->setMethods(['getStatusCode', 'getBody'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse->expects($this->atLeast(1))
            ->method('getStatusCode')
            ->will($this->returnValue('200'));

        $invalidBodyMock = $this->getMockBuilder('GuzzleHttp\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $invalidBodyMock->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue('asldkjaslkdjsadlkj'));

        $mockResponse->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($invalidBodyMock));

        $this->mockRequest = $this->getMockBuilder('GuzzleHttp\Message\Request')
            ->setMethods(['getUri', 'getContents'])
            ->disableOriginalConstructor()
            ->getMock();
            

        $this->mockRequest->expects($this->atLeast(1))
            ->method('getUri')
            ->will($this->returnValue(new Uri('http://test-collector/hello')));

        $this->analyticsResponse = new AnalyticsResponse($this->mockRequest, $mockResponse);


        $mockResponseAsync = $this->getMockBuilder('GuzzleHttp\Promise\Promise')
            ->disableOriginalConstructor()
            ->getMock();

        $this->analyticsResponseAsync = new AnalyticsResponse($this->mockRequest, $mockResponseAsync);


        $mockDebugResponse = $this->getMockBuilder('GuzzleHttp\Message\Response')
            ->setMethods(['getStatusCode', 'getBody'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockDebugResponse->expects($this->atLeast(1))
            ->method('getStatusCode')
            ->will($this->returnValue('200'));

        $bodyMock = $this->getMockBuilder('GuzzleHttp\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $bodyMock->expects($this->atLeast(1))
            ->method('getContents')
            ->will($this->returnValue('{"salutation":"hello world"}'));

        $mockDebugResponse->expects($this->atLeast(1))
            ->method('getBody')
            ->will($this->returnValue($bodyMock));

        $this->analyticsDebugResponse = new AnalyticsResponse($this->mockRequest, $mockDebugResponse);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithWrongResponseValue()
    {
        new AnalyticsResponse($this->mockRequest, new \stdClass());
    }

    public function testStatusCode()
    {
        $this->assertEquals('200', $this->analyticsResponse->getHttpStatusCode());
        $this->assertEquals(null, $this->analyticsResponseAsync->getHttpStatusCode());
    }

    public function testGetUrl()
    {
        $this->assertEquals('http://test-collector/hello', $this->analyticsResponse->getRequestUrl());
        $this->assertEquals('http://test-collector/hello', $this->analyticsResponseAsync->getRequestUrl());
    }

    public function testDebugResponse()
    {
        $this->assertEquals(['salutation' => 'hello world'], $this->analyticsDebugResponse->getDebugResponse());
        $this->assertEquals([], $this->analyticsResponse->getDebugResponse());
    }
}
