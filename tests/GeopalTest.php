<?php
namespace Geopal\Tests;

use Geopal\Geopal as GeoPal;
use Geopal\Http\Client as GeoPalClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

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
            new Response(200, [], $responseData)
        ]);
        $handler = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(
            [
                'base_uri' => 'http://www.test.com/',
                'handler' => $handler
            ]
        );

        $this->geopalClient = new GeoPalClient(null, null, $guzzleClient);

        $geoPal = new Geopal($this->getEmployeeId(), "");
        $geoPal->setClient($this->geopalClient);

        return $geoPal;
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
        $guzzleClient = new GuzzleClient(['base_uri' => $baseUrl]);
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
     * Test if getCompanyFiles returns correct data on success
     *
     * @param $mockResponseData
     * @covers \Geopal\Geopal::getCompanyFiles
     * @dataProvider mockCompanyFilesUploadData
     */
    public function testGetCompanyFiles($mockResponseData)
    {
        $geoPal = $this->getMockedGeoPalObj($mockResponseData);
        $response = $geoPal->getCompanyFiles();

        $this->assertTrue(is_array($response));

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('category', $response);
        $this->assertArrayHasKey('s3file_id', $response);

        $this->assertEquals($mockResponseData['company_file_uploads']['id'], $response['id']);
    }

    /**
     * Test if getCompanyFile returns correct data on success
     *
     * @param $mockResponseData
     * @covers \Geopal\Geopal::getCompanyFile
     * @dataProvider mockCompanyFileUploadData
     */
    public function testGetCompanyFile($mockResponseData)
    {
        $geoPal = $this->getMockedGeoPalObj($mockResponseData);
        $response = $geoPal->getCompanyFile(1064);

        $this->assertTrue(is_array($response));

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('category', $response);
        $this->assertArrayHasKey('s3file_id', $response);

        $this->assertEquals($mockResponseData['company_file_upload']['name'], $response['name']);
    }

    /**
     * Test if addCompanyFile returns correct data on success
     *
     * @param $mockResponseData
     * @covers \Geopal\Geopal::addCompanyFile
     * @dataProvider mockCompanyFileUploadData
     */
    public function testAddCompanyFile($mockResponseData)
    {
        $geoPal = $this->getMockedGeoPalObj($mockResponseData);
        $response = $geoPal->addCompanyFile(
            'test4',
            'Test Category',
            __DIR__. '/test.txt'
        );

        $this->assertTrue(is_array($response));

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('category', $response);
        $this->assertArrayHasKey('s3file_id', $response);
    }

    /**
     * Test if updateCompanyFile returns correct data on success
     *
     * @param $mockResponseData
     * @covers \Geopal\Geopal::updateCompanyFile
     * @dataProvider mockCompanyFileUploadData
     */
    public function testUpdateCompanyFile($mockResponseData)
    {
        $geopal = $this->getMockedGeoPalObj($mockResponseData);
        $response = $geopal->updateCompanyFile(
            1064,
            'test-file-updated.txt',
            'test_category_updated',
            __DIR__. '/test.txt'
        );

        $this->assertTrue(is_array($response));

        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('name', $response);
        $this->assertArrayHasKey('category', $response);
        $this->assertArrayHasKey('s3file_id', $response);
    }

    /**
     * Test if deleteCompanyFile returns correct data on success
     *
     * @param $mockResponseData
     * @covers \Geopal\Geopal::deleteCompanyFile
     * @dataProvider mockCompanyFileUploadDeleteData
     */
    public function testDeleteCompanyFile($mockResponseData)
    {
        $geopal = $this->getMockedGeoPalObj($mockResponseData);
        $response = $geopal->deleteCompanyFile(1065);

        $this->assertTrue($response);
    }

    /**
     * Test if downloadCompanyFile returns correct data on success
     *
     * @param $mockResponseData
     * @covers \Geopal\Geopal::downloadCompanyFile
     * @dataProvider mockCompanyFileUploadDownloadResponse
     */
    public function testDownloadCompanyFile($mockResponseData)
    {
        $geopal = $this->getMockedGeoPalObj($mockResponseData);
        $response = $geopal->downloadCompanyFile(1065);

        $this->assertEquals(200, $response->getResponse()->getStatusCode());
    }



    /**
     * Test the getTeams method returns a valid response
     *
     * @param $mockResponseData
     * @covers       \Geopal\Geopal::getTeams()
     * @dataProvider mockTeamData
     */
    public function testGetTeams($mockResponseData)
    {
        $geoPal = $this->getMockedGeoPalObj($mockResponseData);

        $teamsResponse = $geoPal->getTeams();

        $this->doTeamAssertions($teamsResponse);
    }

    /**
     * Test the getTeam method returns a valid response
     *
     * @param $mockResponseData
     * @covers       \Geopal\Geopal::getTeam()
     * @dataProvider mockTeamData
     */
    public function testGetTeam($mockResponseData)
    {
        $geoPal = $this->getMockedGeoPalObj($mockResponseData);

        $teamsResponse = $geoPal->getTeam(1234);

        $this->doTeamAssertions($teamsResponse);
    }

    /**
     * Test the addTeam method returns a valid response
     *
     * @param $mockResponseData
     * @covers       \Geopal\Geopal::addTeam()
     * @dataProvider mockTeamData
     */
    public function testAddTeam($mockResponseData)
    {
        $geoPal = $this->getMockedGeoPalObj($mockResponseData);

        $teamsResponse = $geoPal->addTeam(
            'Test Team Name',
            1234,
            array(3,5,8,4),
            array(33,483,4)
        );

        $this->doTeamAssertions($teamsResponse);
    }

    /**
     * Test the updateTeam method returns a valid response
     *
     * @param $mockResponseData
     * @covers       \Geopal\Geopal::updateTeam()
     * @dataProvider mockTeamData
     */
    public function testUpdateTeam($mockResponseData)
    {
        $geoPal = $this->getMockedGeoPalObj($mockResponseData);

        $teamsResponse = $geoPal->updateTeam(
            234,
            'Test Team Name',
            1234,
            array(3,5,8,4),
            array(33,483,4)
        );

        $this->doTeamAssertions($teamsResponse);
    }

    /**
     * Test the deleteTeam method returns a valid response
     *
     * @param $mockResponseData
     * @covers       \Geopal\Geopal::deleteTeam()
     * @dataProvider mockTeamDeleteData
     */
    public function testDeleteTeam($mockResponseData)
    {
        $geopal = $this->getMockedGeoPalObj($mockResponseData);
        $response = $geopal->deleteTeam(1065);

        $this->assertTrue($response);
    }

    /**
     * Mock Data for for job Template
     *
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
     *
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

    /**
     * Mock data for company file upload response's
     *
     * @return array
     */
    public function mockCompanyFilesUploadData()
    {
        return self::mockCompanyFileUploadData(true);
    }

    /**
     * Mock data for company file upload response's
     *
     * @param bool $plural
     * @return array
     */
    public function mockCompanyFileUploadData($plural = false)
    {
        return array(
            array(
                array(
                    'status' => true,
                    'company_file_upload'.($plural === true ? 's' : '') =>
                        array(
                            "id" => 1064,
                            "name" => "test4",
                            "category" => "test_category",
                            "company_id" => 2,
                            "s3file_id" => 41,
                            "is_deleted" => false,
                            "updated_on" => "2015-12-01 14:33:26",
                            "updated_by" => 0,
                            "created_on" => "2015-12-01 14:33:26",
                            "created_by" => 0,
                            "s3file" => array(
                                "id" => 41,
                                "uuid" => "cae5d54b-16bd-4b23-93d2-48ef575054af",
                                "bucket" => "geopalapp",
                                "file_name" => "2/some-directory/test-file-32.txt",
                                "mime_type" => "text/plain",
                                "size" => 20,
                                "tmp_file_name" => "/some-directory/test-file-32.txt",
                                "tmp_file_created_on" => "2015-12-01 16:55:46",
                                "company_id" => 2,
                                "is_deleted" => false,
                                "deleted_by" => 0,
                                "created_by" => 10375,
                                "created_on" => "2015-12-01 14:33:26",
                                "updated_on" => "2015-12-01 14:33:26"
                            )
                        )
                )
            )
        );
    }


    public function mockTeamData()
    {
        return array(
            array(
                array(
                    'status' => true,
                    'teams' => array(
                        array(
                            'id' => 1234,
                            'company_id' => 567,
                            'name' => 'Team A',
                            'job_template_id' => 89,
                            'description' => 'This is Team A',
                            'updated_on' => date('Y-m-d H:i:s', time()),
                            'updated_by' => 10,
                            'created_on' => date('Y-m-d H:i:s', time()),
                            'created_by' => 11,
                            'employees' => array(1,2,3,4)
                        )
                    )
                )
            )
        );
    }

    /**
     * Mock data for team delete response
     *
     * @return array
     */
    public function mockTeamDeleteData()
    {
        return array(
            array(
                array(
                    'status' => true,
                    'teams' => true
                )
            )
        );
    }

    /**
     * Mock data for company file upload delete response
     *
     * @return array
     */
    public function mockCompanyFileUploadDeleteData()
    {
        return array(
            array(
                array(
                    'status' => true,
                    'company_file_upload' => true
                )
            )
        );
    }



    /**
     * Mock data for company file upload download response
     *
     * @return array
     */
    public function mockCompanyFileUploadDownloadResponse()
    {
        /*$headers = new HeaderCollection(
            array(
                'date' => new Header('Date', array('Thu, 03 Dec 2015 15:42:45 GMT')),
                'content-disposition' => new Header(
                    'Content-Disposition',
                    array('attachment; filename="test-file-32.txt"')
                ),
            )
        );
        $body = new EntityBody(fopen(__DIR__. '/test.txt', 'r'));

        return array(
            array(
                array(
                    new Response(200, $headers, $body)
                )
            )
        );*/
    }

    /**
     * Helper function to test team test function response's
     *
     * @param $teamsResponse
     */
    private function doTeamAssertions($teamsResponse)
    {
        $this->assertEquals(true, is_array($teamsResponse));

        $this->assertGreaterThan(0, sizeof($teamsResponse));

        $firstTeam = $teamsResponse[0];

        $this->assertArrayHasKey('id', $firstTeam);
        $this->assertArrayHasKey('company_id', $firstTeam);
        $this->assertArrayHasKey('name', $firstTeam);
        $this->assertArrayHasKey('description', $firstTeam);
        $this->assertArrayHasKey('employees', $firstTeam);

        $this->assertTrue(is_array($firstTeam['employees']));
    }
}
