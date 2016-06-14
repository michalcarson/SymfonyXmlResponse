<?php


use SymfonyXmlResponse\Responses\XmlRepeater;
use SymfonyXmlResponse\Responses\XmlResponse;

class IntegrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that even when decorators are added in incorrect order the replacement still occurs
     *
     * @return void
     */
    public function testRepeaterPriority()
    {
        // Arrange
        $response = new XmlResponse();
        $data = [
            'someContent' => [
                '@replaceThis@'
            ]
        ];

        $nestedReplace = [
            '@nestedReplace@'
        ];

        $r1 = new XmlRepeater('@replaceThis@', 'replacedEl', $nestedReplace);
        $r2 = new XmlRepeater('@nestedReplace@', 'nestedEl', ['stuff', 'more stuff']);
        $response->addDecorator($r2);
        $response->addDecorator($r1);

        //Act
        $response->setData($data);
        ob_start();
        $response->sendContent();
        $output = ob_get_clean();

        //Assert
        $this->assertEquals($this->getExpectedXml(), $output);
    }

    private function getExpectedXml()
    {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
               "<document>\n" .
               " <someContent><replacedEl><nestedEl>stuff</nestedEl>\n" .
               "\n" .
               "<nestedEl>more stuff</nestedEl>\n" .
               "</replacedEl>\n" .
               "</someContent>\n" .
               "</document>\n";
    }
}
