<?php

namespace NMeta\Tests;

use NMeta\BadRequestException;
use NMeta\NMeta;

class NMetaTest extends TestCase
{
    public function testSuccess()
    {
        $meta = new NMeta('ios;production;1.0.0;10.2;iphone-x');

        $this->assertEquals('ios', $meta->getPlatform());
        $this->assertEquals('production', $meta->getEnvironment());
        $this->assertEquals('1.0.0', $meta->getVersion());
        $this->assertEquals('10.2', $meta->getDeviceOsVersion());
        $this->assertEquals('iphone-x', $meta->getDevice());
        $this->assertEquals([
            'platform'        => 'ios',
            'environment'     => 'production',
            'version'         => '1.0.0',
            'majorVersion'    => 1,
            'minorVersion'    => 0,
            'patchVersion'    => 0,
            'deviceOsVersion' => '10.2',
            'device'          => 'iphone-x',
        ], $meta->toArray());
    }

    public function testFailureInvalidPlatform()
    {
        $this->expectException(BadRequestException::class);
        $meta = new NMeta('invalid_platform;production;1.0.0;10.2;iphone-x');
    }

    public function testFailureInvalidEnvironment()
    {
        $this->expectException(BadRequestException::class);
        $meta = new NMeta('ios;invalid_environment;1.0.0;10.2;iphone-x');
    }

    public function testFailureMissingVersion()
    {
        $this->expectException(BadRequestException::class);
        $meta = new NMeta('ios;production');
    }

    public function testSuccessWebPlatform()
    {
        $meta = new NMeta('web;production');

        $this->assertEquals('web', $meta->getPlatform());
        $this->assertEquals('production', $meta->getEnvironment());
        $this->assertEquals('0.0.0', $meta->getVersion());
        $this->assertEquals(null, $meta->getDeviceOsVersion());
        $this->assertEquals(null, $meta->getDevice());
        $this->assertEquals([
            'platform'        => 'web',
            'environment'     => 'production',
            'version'         => '0.0.0',
            'majorVersion'    => 0,
            'minorVersion'    => 0,
            'patchVersion'    => 0,
            'deviceOsVersion' => null,
            'device'          => null,
        ], $meta->toArray());
    }

    public function testFailureIMissingOSVersion()
    {
        $this->expectException(BadRequestException::class);
        $meta = new NMeta('ios;production;1.0.0');
    }

    public function testFailureMissingDevice()
    {
        $this->expectException(BadRequestException::class);
        $meta = new NMeta('ios;production;1.0.0;10.2');
    }
}
