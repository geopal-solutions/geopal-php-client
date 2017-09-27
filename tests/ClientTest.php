<?php
namespace Geopal\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Geopal\Http\Client as GeoPalClient;

date_default_timezone_set('Europe/Dublin');

class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GeoPalClient
     */
    private $geopalClient;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, array(), 'test'),
        ]);

        $guzzleClient = new GuzzleClient([
            'base_uri' => 'http://www.test.com/',
            'handler'  => HandlerStack::create($mock)
        ]);

        $this->geopalClient = new GeoPalClient(null, null, $guzzleClient);
    }

    /**
     * @return array
     */
    public static function providerGetSignature()
    {
        return array(
            array(1, 'private key', 'get', 'api/jobs/get', 'Fri, 20 Sep 2013 15:46:18 +0200',
                'MzI3ZGU4ZThlMzc3N2Q0YWRmZmNkY2RkOWVhZWM0N2JiY2M5ZGFlZTI1Y2RlYzE4OTZhNDdkY2I2OWMwOGVmMA=='),
            array(1, 'private key', 'get', 'api/jobs/get', 'Fri, 20 Sep 2013 15:46:18 +0200',
                'MzI3ZGU4ZThlMzc3N2Q0YWRmZmNkY2RkOWVhZWM0N2JiY2M5ZGFlZTI1Y2RlYzE4OTZhNDdkY2I2OWMwOGVmMA==')
        );
    }

    /**
     * @dataProvider providerGetSignature
     */
    public function testGetSignature($employeeId, $privateKey, $verb, $uri, $timestamp, $expectedResult)
    {
        $this->geopalClient->setEmployeeId($employeeId);
        $this->geopalClient->setPrivateKey($privateKey);
        $result = $this->geopalClient->getSignature($verb, $uri, $timestamp);
        $this->assertEquals($expectedResult, $result);
    }

    public function testGet()
    {
        $this->assertEquals('test', $this->geopalClient->get('api/jobs/get')->getBody());
    }
}
