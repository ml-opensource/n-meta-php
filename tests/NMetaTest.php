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

        try {
            $meta = new NMeta('invalid_platform;production;1.0.0;10.2;iphone-x');
        } catch (BadRequestException $e) {
            $expected = 'Client-Meta-Information header: Platform is not supported, should be: android,ios,web - format: platform;environment;version;os-version;device';
            $this->assertEquals($expected, $e->getMessage());
            throw $e;
        }
    }

    public function testFailureInvalidEnvironment()
    {
        $this->expectException(BadRequestException::class);
        $meta = new NMeta('ios;invalid_environment;1.0.0;10.2;iphone-x');
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

    public function testFailureMissingBuildVersion()
    {
        $this->expectException(BadRequestException::class);
        $meta = new NMeta('ios;production');
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

    public function testFailureInvalidAppMajorVersion()
    {
        $this->expectException(BadRequestException::class);

        try {
            $meta = new NMeta('android;staging;1-staging.0.0;Android 12;SM-G975F');
        } catch (BadRequestException $e) {
            $expected = 'Client-Meta-Information header: Invalid Major version, expected integer';
            $this->assertEquals($expected, $e->getMessage());
            throw $e;
        }
    }

    public function testFailureInvalidAppMinorVersion()
    {
        $this->expectException(BadRequestException::class);

        try {
            $meta = new NMeta('android;staging;1.0-staging.0;Android 12;SM-G975F');
        } catch (BadRequestException $e) {
            $expected = 'Client-Meta-Information header: Invalid Minor version, expected integer';
            $this->assertEquals($expected, $e->getMessage());
            throw $e;
        }
    }

    public function testFailureInvalidAppPatchVersion()
    {
        $this->expectException(BadRequestException::class);

        try {
            $meta = new NMeta('android;staging;1.0.0-staging;Android 12;SM-G975F');
        } catch (BadRequestException $e) {
            $expected = 'Client-Meta-Information header: Invalid Patch version, expected integer';
            $this->assertEquals($expected, $e->getMessage());
            throw $e;
        }
    }

    public function testFailureInvalidAmountOfVersionSegments()
    {
        $this->expectException(BadRequestException::class);

        try {
            $meta = new NMeta('android;staging;1.0;Android 12;SM-G975F');
        } catch (BadRequestException $e) {
            $expected = 'Client-Meta-Information header: Invalid app version, invalid amount of segments. Expected semver [x.y.z]';
            $this->assertEquals($expected, $e->getMessage());
            throw $e;
        }
    }
}
