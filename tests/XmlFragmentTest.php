<?php

/**
 * Description of XmlResponseTest
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

use SymfonyXmlResponse\Responses\XmlResponse;

class XmlResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFragmentFromArray()
    {
        $response = new XmlResponse();
        $fragment = $response->getFragment('file', array('name' => 'file1.txt', 'size' => 'large'));
        $this->assertFragmentEquals('<name>file1.txt</name><size>large</size>', $fragment, '', 'file');
    }

    public function testGetFragmentFromString()
    {
        $response = new XmlResponse();
        $fragment = $response->getFragment('file', 'dissertation.rtf');
        $this->assertFragmentEquals('dissertation.rtf', $fragment, '', 'file');
    }

    public function testGetFragmentEmpty()
    {
        $response = new XmlResponse();
        $fragment = $response->getFragment('file', '');
        $this->assertFragmentEquals('', $fragment, '', 'file');
    }

    protected function assertFragmentEquals($expected, $fragment, $message = '', $wrapper = '')
    {
        // strip line feeds and leading whitespace
        $content = preg_replace('/\n(?:\s)*/', '', $fragment);

        $this->assertEquals('<' . $wrapper . '>', substr($content, 0, strlen($wrapper) + 2), 'XML root element did not match ' . $wrapper);
        $content = trim(substr($content, strlen($wrapper) + 2));

        $this->assertEquals('</' . $wrapper . '>', substr($content, (strlen($wrapper) + 3) * -1), 'XML root element was not terminated correctly');
        $content = trim(substr($content, 0, (strlen($wrapper) + 3) * -1));

        $this->assertEquals($expected, $content, $message);

    }
}
