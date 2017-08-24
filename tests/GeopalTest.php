<?php
namespace Geopal\Tests;

use Geopal\Geopal as GeoPal;
use Geopal\Http\Client as GeoPalClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

date_default_timezone_set('Europe/Dublin');

/**
 * @todo    Fix Mock Data
 *
 * Class GeopalTest
 * @package Geopal\Tests
 * @author mark.mccullagh@geopal-solutions.com
 */
class GeopalTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GeoPal
     */
    public $geoPal;

    /**
     * @var int
     */
    private $templateId;

    /**
     * @var int
     */
    private $employeeId;

    /**
     * @var int
     */
    private $jobId;

    /**
     * @var string
     */
    private $dateTimeFrom;

    /**
     * @var string
     */
    private $dateTimeTo;


    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->setTemplateId(29);
        $this->setEmployeeId(2);
        $this->setJobId(226226);
        $this->setDateTimeFrom('2013-01-01 00:00');
        $this->setDateTimeTo('2013-12-31 00:00');
    }

    /**
     * @param int $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * @return int
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param int $employeeId
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return int
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param int $jobId
     */
    public function setJobId($jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * @return int
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * @param string $dateTimeFrom
     */
    public function setDateTimeFrom($dateTimeFrom)
    {
        $this->dateTimeFrom = $dateTimeFrom;
    }

    /**
     * @return string
     */
    public function getDateTimeFrom()
    {
        return $this->dateTimeFrom;
    }

    /**
     * @param string $dateTimeTo
     */
    public function setDateTimeTo($dateTimeTo)
    {
        $this->dateTimeTo = $dateTimeTo;
    }

    /**
     * @return string
     */
    public function getDateTimeTo()
    {
        return $this->dateTimeTo;
    }

    /**
     * @param array $data
     * @return Geopal
     */
    private function getMockedGeoPalObj(array $data)
    {
        $responseData = json_encode($data);

        $mock = new MockHandler([
            new Response(200, array(), $responseData),
        ]);

        $guzzleClient = new GuzzleClient([
            'base_uri' => 'http://www.test.com/',
            'handler'  => HandlerStack::create($mock)
        ]);

        $geopalClient = new GeoPalClient(null, null, $guzzleClient);

        $geoPal = new Geopal($this->getEmployeeId(), "");
        $geoPal->setClient($geopalClient);

        return $geoPal;
    }

    /**
     * tests that an expection is thrown when an invalid method is called
     * @param array $testData
     * @dataProvider mockCreateAndAssignJobData
     * @expectedException \Geopal\Exceptions\GeopalException
     */
    public function testNotImplementedMethod(array $testData)
    {
        $geoPal = $this->getMockedGeoPalObj($testData);

        $job = $geoPal->notImplemented($this->getTemplateId());
    }
    /**
     * tests that the __call() function handles missing required parameters
     * @param array $testData
     * @dataProvider mockCreateAndAssignJobData
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMandatoryParameterError(array $testData)
    {
        $geoPal = $this->getMockedGeoPalObj($testData);

        $geoPal->createAndAssignJob();
    }
    /**
     * tests the create and assign job method returns a valid response and if the data is updated correctly
     * @covers       \Geopal\Geopal::createAndAssignJob
     * @param array $testData
     * @dataProvider mockCreateAndAssignJobData
     */
    public function testCreateAndAssignJob(array $testData)
    {
        $geoPal = $this->getMockedGeoPalObj($testData);

        $job = $geoPal->createAndAssignJob($this->getTemplateId());

        $this->assertEquals(true, is_array($job));
        $this->assertEquals($this->getTemplateId(), $job['job_template']['id']);
    }

    /**
     * Test the create job method returns valid response including the new Job Details
     * @covers \Geopal\Geopal::createJob
     * @param $testData
     * @dataProvider mockJobData
     */
    public function testCreateJob($testData)
    {
        $geoPal = $this->getMockedGeoPalObj($testData);

        $job = $geoPal->createJob($this->getTemplateId());

        $this->assertEquals(true, is_array($job));
        $this->assertEquals($this->getJobId(), $job['id']);
    }

    /**
     * Test if the AssignJob method returns a valid response with the updated data
     * @covers       \Geopal\Geopal::assignJob
     * @dataProvider mockJobAssignData
     */
    public function testAssignJob($testData)
    {
        $geoPal = $this->getMockedGeoPalObj($testData);


        $response = $geoPal->assignJob(
            $this->getJobId(),
            new \DateTime(date('Y-m-d H:i:s', time())),
            $this->getEmployeeId()
        );

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('assigned_to', $response);
        $this->assertArrayHasKey('start_date', $response);

        $this->assertArrayHasKey('id', $response['assigned_to']);
    }


    /**
     * test if reassigned data is applied and returned in response
     * @covers \Geopal\Geopal::reassignJob
     */
    public function testReassignJob()
    {
        $geoPal = $this->getMockedGeoPalObj(
            array(
                'status' => true,
                'tag' => '',
                'employee' => array(),
                'company' => array(),
                'job' => array(
                    'id' => $this->getJobId(),
                    'assigned_to' => array(
                        'id' => $this->getEmployeeId()
                    ),
                    'start_date' => date('Y-m-d H:i:s', time())
                )
            )
        );

        $response = $geoPal->reassignJob(
            $this->getJobId(),
            $this->getEmployeeId(),
            new \DateTime(date('Y-m-d H:i:s', time()))
        );

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('assigned_to', $response);
        $this->assertArrayHasKey('start_date', $response);
        $this->assertArrayHasKey('id', $response['assigned_to']);

        $this->assertEquals($this->getJobId(), $response['id']);
        $this->assertEquals($this->getEmployeeId(), $response['assigned_to']['id']);
    }


    /**
     * tests if geopal's client is of the correct instance mocked or unmocked
     * @covers \Geopal\Geopal::getClient
     */
    public function testGetClient()
    {
        $geopalMocked = $this->getMockedGeoPalObj(array());
        $this->assertInstanceOf('\geopal\http\Client', $geopalMocked->getClient());

        $geopalUnMocked = new Geopal("", "");
        $this->assertInstanceOf('\Geopal\Http\Client', $geopalUnMocked->getClient());
    }


    /**
     * tests if the setClient method set the client correctly
     * @covers \Geopal\Geopal::setClient
     */
    public function testSetClient()
    {
        $baseUrl = 'http://www.test.com/';
        $guzzleClient = new GuzzleClient([
            'base_uri' => $baseUrl
        ]);
        $geopal = new Geopal($this->getEmployeeId(), "");
        $geopal->setClient($guzzleClient);

        $this->assertInstanceOf('\GuzzleHttp\Client', $geopal->getClient());
        $this->assertTrue($guzzleClient === $geopal->getClient());
    }


    /**
     * test if the get job by id method returns the correct details
     * @covers \Geopal\Geopal::getJobById
     */
    public function testGetJobById()
    {
        $geoPal = $this->getMockedGeoPalObj(
            array(
                'status' => true,
                'job' => array(
                    'id' => $this->getJobId()
                )
            )
        );

        $job = $geoPal->getJobById($this->getJobId());

        $this->assertEquals(true, is_array($job));
        $this->assertEquals($this->getJobId(), $job['id']);
    }


    /**
     * test the data returned for jobs between date range
     * @covers \Geopal\Geopal::getJobsBetweenDateRange
     */
    public function testGetjobsBetweenDateRange()
    {
        $geoPal = $this->getMockedGeoPalObj(
            array(
                'status' => true,
                'jobs' => array(
                    array('id' => 1),
                    array('id' => 2)
                )
            )
        );

        $jobs = $geoPal->getJobsBetweenDateRange(
            date('Y-m-d H:i:s', strtotime('next monday')),
            date('Y-m-d H:i:s', strtotime('last monday'))
        );

        $this->assertEquals(true, is_array($jobs));
        $this->assertGreaterThan(0, sizeof($jobs));
        $this->assertArrayHasKey('id', $jobs[0]);
    }

    /**
     *
     * @covers \Geopal\Geopal::getEmployeesList
     */
    public function testGetEmployeesList()
    {
        $geoPal = $this->getMockedGeoPalObj(
            array(
                'status' => true,
                'employees' => array(
                    'id' => $this->getEmployeeId(),
                    'title' => '',
                    'first_name' => '',
                    'last_name' => ''
                )
            )
        );

        $employees = $geoPal->getEmployeesList();

        $this->assertArrayHasKey('id', $employees);
        $this->assertArrayHasKey('title', $employees);
        $this->assertArrayHasKey('first_name', $employees);
        $this->assertArrayHasKey('last_name', $employees);
        $this->assertEquals($this->getEmployeeId(), $employees['id']);

    }

    /**
     * Test the get jobs templates method returns valid response
     * @covers       \Geopal\Geopal::getJobTemplates()
     */
    public function testGetJobTemplates()
    {
        $geoPal = $this->getMockedGeoPalObj(
            array(
                'status' => true,
                'job_templates' => array(
                    array(
                        'job_template_id' => $this->getTemplateId(),
                        'name' => '',
                        'is_deleted' => false,
                        'updated_on' => date('Y-m-d H:i:s', time())
                    )
                )
            )
        );

        $templates = $geoPal->getJobTemplates();

        $this->assertEquals(true, is_array($templates));

        $this->assertGreaterThan(0, sizeof($templates));
        $this->assertArrayHasKey('job_template_id', $templates[0]);

        $this->assertArrayHasKey('name', $templates[0]);
        $this->assertArrayHasKey('is_deleted', $templates[0]);
        $this->assertArrayHasKey('updated_on', $templates[0]);
    }

    /**
     * Tests if getJobTemplateById returns a valid response
     * @param array $testData
     * @dataProvider mockJobTemplateData
     */
    public function testGetJobTemplateById(array $testData)
    {
        $geoPal = $this->getMockedGeoPalObj($testData);

        $response = $geoPal->getJobTemplateById($this->getJobId());

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('is_deleted', $response);

    }


    /**
     * Tests if the update Employee by id returns a valid response and if the id is updated correctly
     * @covers \Geopal\Geopal::updateEmployeeById
     */
    public function testUpdateEmployeeById()
    {

        $employeeData = array(
            'id' => $this->getEmployeeId(),
            'first_name' => "Bob",
            'last_name' => "Smith",
            'email' => "bob.smith@domain.com"
        );

        $geoPal = $this->getMockedGeoPalObj(
            array(
                'status' => true,
                'employee_data' => array_merge($employeeData, array())
            )
        );

        $response = $geoPal->updateEmployeeById(
            $this->getEmployeeId(),
            "",
            "",
            $employeeData['first_name'],
            $employeeData['last_name'],
            $employeeData['email']
        );

        $this->assertEquals(true, is_array($response));

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('first_name', $response);
        $this->assertArrayHasKey('last_name', $response);
        $this->assertArrayHasKey('email', $response);

        $this->assertEquals($this->getEmployeeId(), $response['id']);
        $this->assertEquals($employeeData['first_name'], $response['first_name']);
        $this->assertEquals($employeeData['last_name'], $response['last_name']);
        $this->assertEquals($employeeData['email'], $response['email']);
    }


    /**
     * Mock Data for for job Template
     * @return array
     */
    public function mockJobTemplateData()
    {
        return array(
            array(
                array(
                    'status' => true,
                    'job_template' => array(
                        'id' => $this->getJobId(),
                        'name' => null,
                        'is_deleted' => false,
                        'updated_on' => date('Y-m-d H:i:s', time())
                    )
                ),
                array(
                    array(
                        'status' => true,
                        'job_template' => array(
                            'id' => $this->getJobId(),
                            'name' => null,
                            'is_deleted' => false,
                            'updated_on' => date('Y-m-d H:i:s', time())
                        )
                    )
                )
            )
        );
    }

    /**
     * Test data for jobs
     * @return array
     */
    public function mockCreateAndAssignJobData()
    {
        return array(
            array(
                array(
                    'status' => true,
                    'tag' => '',
                    'employee' => array(),
                    'company' => array(),
                    'job' => array(
                        'id' => 226226,
                        'job_template' => array(
                            'id' => 29
                        )
                    )
                )
            )
        );
    }

    /**
     * Test data for jobs
     * @return array
     */
    public static function mockJobData()
    {
        return array(
            array(
                array(
                    'status' => true,
                    'tag' => '',
                    'employee' => array(),
                    'company' => array(),
                    'job' => array(
                        'id' => 226226,
                        'job_template' => array(
                            'id' => 29
                        )
                    )
                )
            )
        );
    }

    /**
     * @return array
     */
    public function mockJobAssignData()
    {
        return array(
            array(
                array(
                    'status' => true,
                    'tag' => '',
                    'employee' => array(),
                    'company' => array(),
                    'job' => array(
                        'id' => $this->getJobId(),
                        'assigned_to' => array(
                            'id' => $this->getEmployeeId()
                        ),
                        'start_date' => date('Y-m-d H:i:s', time())
                    )
                )
            )
        );
    }

}
