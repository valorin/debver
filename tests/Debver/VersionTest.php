<?php
namespace Debver\Test;

require_once __DIR__ ."/../../src/Debver/Version.php";
require_once __DIR__ ."/../../src/Debver/VersionException.php";

use Debver\Version;
use Debver\VersionException;
use PHPUnit_Framework_TestCase;

class VersionTest extends PHPUnit_Framework_TestCase
{
    protected $versions = Array(
        '2.2.20-1ubuntu1.1', '2:1.0.3-0ubuntu4.1', '4.2.1-6', '2.6.5-1ubuntu6',
        '2.1.10-1ubuntu2.3', '3.3.8', '0.88.1', '1.3.11', '1.2.16-2ubuntu4.1',
        '2.8.9', '3.59-1ubuntu1.1', '1.1.1-4ubuntu2.4', '1.14.15',
        '0.8.16~exp12ubuntu10.1', '5.3.2-1ubuntu4.17', '5.13', '0.9.6k',
        '2.20.1-0ubuntu2.1', '2.3.5-1ubuntu4.8.04.7', '2.1.10-1ubuntu2.6',
        '0.8.2-2ubuntu2.2', '1.0.5-4ubuntu0.2', '2.4.4-1ubuntu2.1', '0.9.6i',
        '0.97.5+dfsg-1ubuntu0.11.04.2~10.04.1', '1.900.1-7ubuntu0.10.04.1',
        '1.4.11-3ubuntu1.11.10.1', '5.2.6-2ubuntu4.3', '4.0.3', '0.6.46.1',
        '1:4.3p2-5ubuntu1.2', '1.08-2', '1.6.3p3', '1.0.3-0ubuntu2.1',
        '1.5.3-1ubuntu1.2', '1:4.6p1-5ubuntu0.5', '1.7.4p6-1ubuntu2.1',
        '1:4.2.4p8+dfsg-1ubuntu2.1', '7.15.1-1ubuntu3.2', '2.2.11-2ubuntu2.3',
        '2:3.4.7~dfsg-1ubuntu3.8', '2.12.1-0ubuntu8', '5.1.2-1ubuntu3.6',
        '0.7.2-1ubuntu2~0.10.10.1', '0.9.8a-7ubuntu0.13', '2.2.12-1ubuntu2.1',
        '5.2.4-2ubuntu5.26', '0.97.3+dfsg-1ubuntu0.11.10.1',
        '0.96.1+dfsg-0ubuntu0.10.04.1', '2.7.8.dfsg-2ubuntu0.1',
        '1:4.0.18.1-9ubuntu0.1', '1.14.24ubuntu1.2', '1.4.4-5ubuntu2.1'
    );


    public function testCompare()
    {
        $ver1array = $this->versions;
        $ver2array = $this->versions;
        shuffle($ver1array);
        shuffle($ver2array);

        foreach ($ver1array as $ver1) {
            foreach ($ver2array as $ver2) {
                $compare = Version::compare($ver1, $ver2);
                $dpkg    = Version::compareWithDpkg($ver1, $ver2);

                $this->assertEquals($dpkg, $compare);
            }
        }
    }


    public function testGetEpochSet()
    {
        $version = new Version("1:4.3p2-5ubuntu1.2");
        $this->assertEquals(1, $version->getEpoch());
    }

    public function testGetEpochNotSet()
    {
        $version = new Version("4.3p2-5ubuntu1.2");
        $this->assertNull($version->getEpoch());
    }


    public function testGetUpstreamWithEpochAndRevision()
    {
        $version = new Version("1:4.3p2-beta-5ubuntu1.2");
        $this->assertEquals("4.3p2-beta", $version->getUpstream());
    }

    public function testGetUpstreamWithEpoch()
    {
        $version = new Version("1:4.3p2");
        $this->assertEquals("4.3p2", $version->getUpstream());
    }

    public function testGetUpstreamWithRevision()
    {
        $version = new Version("4.3p2-alpha-5ubuntu1.2");
        $this->assertEquals("4.3p2-alpha", $version->getUpstream());
    }

    public function testGetUpstreamNoEpochOrRevision()
    {
        $version = new Version("4.3p2~beta");
        $this->assertEquals("4.3p2~beta", $version->getUpstream());
    }


    public function testGetRevisionSet()
    {
        $version = new Version("1:4.3p2-beta-5ubuntu1.2");
        $this->assertEquals("5ubuntu1.2", $version->getRevision());
    }

    public function testGetRevisionNotSet()
    {
        $version = new Version("1:4.3p2~5ubuntu1.2");
        $this->assertNull($version->getRevision());
    }
}
