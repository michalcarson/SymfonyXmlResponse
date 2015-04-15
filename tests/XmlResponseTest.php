<?php

/**
 * Description of XmlResponseTest
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

require '../vendor/autoload.php';
use SymfonyXmlResponse\Responses\XmlResponse;

class XmlResponseTest extends \PHPUnit_Framework_TestCase {

    public function testConstructorEmptyCreatesXmlObject()
    {
        $response = new XmlResponse();
        $this->assertContentEquals('', $response);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithArrayThrowsException()
    {
        $response = new XmlResponse(array(0, 1, 2, 3));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonAlphaTagNameThrowsException()
    {
        $response = new XmlResponse(array('2foo' => 'bar'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonAlphaTagNameThrowsException2()
    {
        $response = new XmlResponse(array('$foo' => 'bar'));
    }

    public function testConstructorWithAssocArrayCreatesXmlObject()
    {
        $response = new XmlResponse(array('foo' => 'bar'));
        $this->assertContentEquals('<foo>bar</foo>', $response);
    }

    public function testConstructorWithSimpleTypes()
    {
        $response = new XmlResponse('foo');
        $this->assertContentEquals('', $response);

        $response = new XmlResponse(['zero' => 0]);
        $this->assertContentEquals('<zero>0</zero>', $response);

        $response = new XmlResponse(['float' => 0.1]);
        $this->assertContentEquals('<float>0.1</float>', $response);

        $response = new XmlResponse(['true' => true]);
        $this->assertContentEquals('<true>1</true>', $response);

        $response = new XmlResponse(['false' => false]);
        $this->assertContentEquals('<false></false>', $response);
    }

    public function testConstructorWithCustomStatus()
    {
        $response = new XmlResponse(array(), 202);
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function testConstructorAddsContentTypeHeader()
    {
        $response = new XmlResponse();
        $this->assertEquals('application/xml', $response->headers->get('Content-Type'));
    }

    public function testConstructorWithCustomHeaders()
    {
        $response = new XmlResponse(array(), 200, array('ETag' => 'foo'));
        $this->assertEquals('application/xml', $response->headers->get('Content-Type'));
        $this->assertEquals('foo', $response->headers->get('ETag'));
    }

    public function testConstructorWithCustomContentType()
    {
        $headers = array('Content-Type' => 'application/vnd.acme.blog-v1+xml');

        $response = new XmlResponse(array(), 200, $headers);
        $this->assertEquals('application/vnd.acme.blog-v1+xml', $response->headers->get('Content-Type'));
    }

    public function testChangingRootElementName()
    {
        $response = new XmlResponse();
        $response->root_element_name = 'pericles';
        $response->setData(['foo' => 'baz']);
        $this->assertContentEquals('<foo>baz</foo>', $response, '', 'pericles');
    }

    public function testXmlNodeAttributes()
    {
        $response = new XmlResponse(array(
            'foo' => array(
                '@argle' => 'bargle',
                '@bing' => 'gong',
                'foo' => 'bar'
            )
        ));
        $this->assertContentEquals('<foo argle="bargle" bing="gong">bar</foo>', $response);
    }

    public function testCreate()
    {
        $response = XmlResponse::create(array('foo' => 'bar'), 204);

        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('<foo>bar</foo>', $response);
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testStaticCreateEmptyXmlObject()
    {
        $response = XmlResponse::create();
        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('', $response);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStaticCreateThrowsException()
    {
        $response = XmlResponse::create(array(0, 1, 2, 3));
        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('[0,1,2,3]', $response);
    }

    public function testStaticCreateXmlObject()
    {
        $response = XmlResponse::create(array('foo' => 'bar'));
        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('<foo>bar</foo>', $response);
    }

    public function testStaticCreateWithSimpleTypes()
    {
        $response = XmlResponse::create('foo');
        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('', $response);

        $response = XmlResponse::create(['zero' => 0]);
        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('<zero>0</zero>', $response);

        $response = XmlResponse::create(['float' => 0.1]);
        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('<float>0.1</float>', $response);

        $response = XmlResponse::create(['true' => true]);
        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('<true>1</true>', $response);

        $response = XmlResponse::create(['false' => false]);
        $this->assertInstanceOf('SymfonyXmlResponse\Responses\XmlResponse', $response);
        $this->assertContentEquals('<false></false>', $response);
    }

    public function testStaticCreateWithCustomStatus()
    {
        $response = XmlResponse::create(array(), 202);
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function testStaticCreateAddsContentTypeHeader()
    {
        $response = XmlResponse::create();
        $this->assertEquals('application/xml', $response->headers->get('Content-Type'));
    }

    public function testStaticCreateWithCustomHeaders()
    {
        $response = XmlResponse::create(array(), 200, array('ETag' => 'foo'));
        $this->assertEquals('application/xml', $response->headers->get('Content-Type'));
        $this->assertEquals('foo', $response->headers->get('ETag'));
    }

    public function testStaticCreateWithCustomContentType()
    {
        $headers = array('Content-Type' => 'application/vnd.acme.blog-v1+xml');

        $response = XmlResponse::create(array(), 200, $headers);
        $this->assertEquals('application/vnd.acme.blog-v1+xml', $response->headers->get('Content-Type'));
    }

    public function testStaticCreateChangingRootElementName()
    {
        $response = XmlResponse::create();
        $response->root_element_name = 'pericles';
        $response->setData(['foo' => 'baz']);
        $this->assertContentEquals('<foo>baz</foo>', $response, '', 'pericles');
    }

    protected function assertContentEquals($expected, $response, $message = '', $wrapper = 'document')
    {
        // strip line feeds and leading whitespace
        $content = preg_replace('/\n(?:\s)*/', '', $response->getContent());

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', substr($content, 0, 38), 'XML did not begin with proper signature');
        $content = trim(substr($content, 38));

        if (strlen($expected)) {

            $this->assertEquals('<' . $wrapper . '>', substr($content, 0, strlen($wrapper) + 2), 'XML root element did not match ' . $wrapper);
            $content = trim(substr($content, strlen($wrapper) + 2));

            $this->assertEquals('</' . $wrapper . '>', substr($content, (strlen($wrapper) + 3) * -1), 'XML root element was not terminated correctly');
            $content = trim(substr($content, 0, (strlen($wrapper) + 3) * -1));

            $this->assertEquals($expected, $content, $message);

        } else {

            $this->assertEquals('<' . $wrapper . '/>', $content, $message);

        }

    }
}
