<?php

namespace NMeta\Tests;

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
}
