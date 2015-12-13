<?php

/**
 * Test the XmlRepeater class.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

use SymfonyXmlResponse\Responses\XmlRepeater;

class XmlRepeaterTest extends \PHPUnit_Framework_TestCase
{
    public function testRepeaterText()
    {
        $rep = new XmlRepeater('@place@', 'element', ['stuff', 'more stuff']);
        $result = $rep->run('<elements>@place@</elements>');
        $this->assertFragmentEquals('<element>stuff</element><element>more stuff</element>', $result, '', 'elements');
    }

    public function testRepeaterSingleElementArray()
    {
        $rep = new XmlRepeater('@place@', 'element', [['motto' => 'yolo']]);
        $result = $rep->run('<elements>@place@</elements>');
        $this->assertFragmentEquals('<element><motto>yolo</motto></element>', $result, '', 'elements');
    }

    public function testRepeaterMultiElementArray()
    {
        $rep = new XmlRepeater('@place@', 'element', [
            ['motto' => 'yolo'],
            ['motto' => 'no ragrets'],
            ['motto' => 'turn on, tune in, drop out']
        ]);
        $result = $rep->run('<elements>@place@</elements>');
        $this->assertFragmentEquals(
            '<element><motto>yolo</motto></element>' .
            '<element><motto>no ragrets</motto></element>' .
            '<element><motto>turn on, tune in, drop out</motto></element>',
            $result, '', 'elements');
    }

    public function testRepeaterFailsToReplace()
    {
        $rep = new XmlRepeater('@place@', 'element', ['stuff', 'more stuff']);
        $result = $rep->run('<elements>@no place@</elements>');
        $this->assertFragmentEquals('@no place@', $result, '', 'elements');
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
