<?php

/**
 * Test the decorator functionality of XmlResponse.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

use SymfonyXmlResponse\Responses\XmlResponse;
use SymfonyXmlResponse\Responses\XmlDecoratorInterface;

class XmlDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddDecorator()
    {
        $dec = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');
        $xr = new XmlResponse();
        $xr->addDecorator($dec);
        $this->assertCount(1, $xr->getDecorators());
    }

    public function testMultipleDecorators()
    {
        $dec1 = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');
        $dec2 = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');
        $dec3 = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');

        $xr = new XmlResponse();
        $xr->addDecorator($dec1);
        $xr->addDecorator($dec2);
        $xr->addDecorator($dec3);

        $this->assertCount(3, $xr->getDecorators());
    }

    public function testMultipleDecoratorsEachGetCalled()
    {
        $dec1 = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');
        $dec1->shouldReceive('run')->once()->andReturn('stuff1');
        $dec2 = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');
        $dec2->shouldReceive('run')->once()->andReturn('stuff2');
        $dec3 = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');
        $dec3->shouldReceive('run')->once()->andReturn('stuff3');

        $xr = new XmlResponse();
        $xr->addDecorator($dec1);
        $xr->addDecorator($dec2);
        $xr->addDecorator($dec3);
        $response = $xr->sendContent();

        $this->assertEquals('stuff3', $response->getContent());
    }
}
