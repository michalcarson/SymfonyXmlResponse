<?php

/**
 * Test the decorator functionality of XmlResponse.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */

use SymfonyXmlResponse\Responses\XmlResponse;

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
        $dec1->shouldReceive('isSuccess')->andReturn(true);
        $dec2 = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');
        $dec2->shouldReceive('run')->once()->andReturn('stuff2');
        $dec2->shouldReceive('isSuccess')->andReturn(true);
        $dec3 = Mockery::mock('SymfonyXmlResponse\Responses\XmlDecoratorInterface');
        $dec3->shouldReceive('run')->once()->andReturn('stuff3');
        $dec3->shouldReceive('isSuccess')->andReturn(true);

        $xr = new XmlResponse();
        $xr->addDecorator($dec1);
        $xr->addDecorator($dec2);
        $xr->addDecorator($dec3);

        ob_start();
        $response = $xr->sendContent();
        ob_end_clean();

        $this->assertEquals('stuff3', $response->getContent());
    }
}
