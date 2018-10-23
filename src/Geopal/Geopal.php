<?php

/**
 *
 */
namespace Geopal;

use Geopal\Http\Client;
use Geopal\Exceptions\GeopalException;
use Geopal\Http\Response;

/**
 * Class Geopal
 * @package Geopal
 */
class Geopal
{
    /**
     * @var int
     */
    protected $employeeId;

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param $employeeId
     * @param $privateKey
     * @param $apiUrl
     */
    public function __construct($employeeId, $privateKey, $apiUrl = null)
    {
        $this->setEmployeeId($employeeId);
        $this->setPrivateKey($privateKey);
        $this->client = new Client($this->getEmployeeId(), $this->getPrivateKey(), null, $apiUrl);
    }

    /**
     * @param $array
     * @param $key
     * @return mixed
     * @throws Exceptions\GeopalException
     */
    protected function checkPropertyAndReturn($array, $key)
    {
        if (is_array($array) && array_key_exists($key, $array) && array_key_exists('status', $array)) {
            if ($array['status'] == true) {
                return $array[$key];
            } else {
                throw new GeopalException(@$array['error_message'], @$array['error_code']);
            }
        } elseif (is_array($array) && array_key_exists('status', $array)) {
            throw new GeopalException(@$array['error_message'], @$array['error_code']);
        } else {
            throw new GeopalException('Invalid data or key not found');
        }
    }


    /**
     * @param $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $employeeId
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
     * @param $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return string
     */
    protected function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Allows creating a job in GeoPal
     *
     * @param int $templateId The ID of the job template to create the job from
     * @param array $params
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createAndAssignJob($templateId, array $params = array())
    {
        $job = $this->client->post(
            'api/jobs/createandassign',
            array_merge(array('template_id' => $templateId), $params)
        )->json();
        return $this->checkPropertyAndReturn($job, 'job');
    }


    /**
     * Allows creating a job in GeoPal
     *
     * @param int $templateId The ID of the job template to create the job from
     * @param array $params Set of information for job creation
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createJob($templateId, array $params = array())
    {
        $job = $this->client->post(
            'api/jobs/create',
            array_merge(array('template_id' => $templateId), $params)
        )->json();
        return $this->checkPropertyAndReturn($job, 'job');
    }

    /**
     * Allows for assigning a job to an employee.
     *
     * @param int $jobId The ID of the target job
     * @param \DateTime $startDateTime Start date and time for the job
     * @param int $assignedToEmployeeId The ID of the employee to assign the job to
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function assignJob($jobId, $startDateTime, $assignedToEmployeeId)
    {
        $job = $this->client->post(
            'api/jobs/assign',
            array(
                'job_id' => $jobId,
                'start_date_time' => $startDateTime->format('Y-m-d H:i:s'),
                'employee_id' => $assignedToEmployeeId
            )
        )->json();
        return $this->checkPropertyAndReturn($job, 'job');
    }

    /**
     * Reassigns a job to another employee
     *
     * @param int $jobId The ID of the target job
     * @param int $employeeReassignedToId The ID of the employee to assign the job to
     * @param \DateTime $startDateTime Start date and time for the job
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reassignJob($jobId, $employeeReassignedToId, $startDateTime)
    {
        $job = $this->client->post(
            'api/jobs/reassign',
            array(
                'job_id' => $jobId,
                'employee_reassigned_to_id' => $employeeReassignedToId,
                'start_date_time' => ($startDateTime instanceof \DateTime) ? $startDateTime->format('Y-m-d H:i:s') : ''
            )
        )->json();
        return $this->checkPropertyAndReturn($job, 'job');
    }

    /**
     * Allows for unassigning of an employee from a job.
     *
     * @param int $jobId The ID of the target job
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unassignJob($jobId)
    {
        $job = $this->client->post(
            'api/jobs/unassign',
            array(
                'job_id' => $jobId
            )
        )->json();
        return $this->checkPropertyAndReturn($job, 'job');
    }

    /**
     * Returns the details of a job when a job is is received
     *
     * @param int $jobId The ID of the target job
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobById($jobId)
    {
        $jobs = $this->client->get('api/jobs/get', array('job_id' => $jobId))->json();
        return $this->checkPropertyAndReturn($jobs, 'job');
    }

    /**
     * Returns the details of a job when a job identifier is received
     *
     * @param int $jobIdentifier The unique identifier of the target job
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobByIdentifier($jobIdentifier)
    {
        $jobs = $this->client->get('api/jobs/getbyidentifier', array('job_identifier' => $jobIdentifier))->json();
        return $this->checkPropertyAndReturn($jobs, 'job');
    }

    /**
     * Returns a detailed list of Jobs created within the target date range.
     *
     * @param string|\DateTime $dateTimeFrom Date and time to list entries from. (YYYY-MM-DD HH:MI:SS)
     * @param string|\DateTime $dateTimeTo Date and time to list entries to.  (YYYY-MM-DD HH:MI:SS)
     * @param int|null $jobStatusId {optional} parameter which allows to filter by job status id
     * @param int|null $jobTemplateId {optional} parameter which allows to filter by job template id
     * @param array|null $type {optional} Can be set to created_on, updated_on or completed_and_synced_date_time, assigned_date_time, defaults to updated_on. Created on is the time the job was created on the server, updated on is the time the job was updated on the server.
    completed_and_synced_date_time indicates when the job has fully synced and also marked as completed and requires version of geopal app >= 1.18.x
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobsBetweenDateRange($dateTimeFrom, $dateTimeTo, $jobStatusId = null, $jobTemplateId = null, $type = null)
    {
        $jobs = $this->client->get(
            'api/jobsearch/ids',
            array(
                'date_time_from' => ($dateTimeFrom instanceof \DateTime) ? $dateTimeFrom->format('Y-m-d H:i:s') : $dateTimeFrom,
                'date_time_to' => ($dateTimeTo instanceof \DateTime) ? $dateTimeTo->format('Y-m-d H:i:s') : $dateTimeTo,
                'job_status_id' => $jobStatusId,
                'job_template_id' => $jobTemplateId,
                'type' => $type
                )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'jobs');
    }

    /**
     * Allows you to assign another existing Address in GeoPal to a Job.
     * If passed doesn't exist yet it will create new one otherwise it will update contact entry using passed address arguments. Address arguments have prefix “address_”.
     * IMPORTANT: Either address_id or address_identifier are required.
     *
     * @param int $jobId The id of the job.
     * @param array $address Set of the address information
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeJobAddress($jobId, $address = array())
    {
        $jobs = $this->client->post(
            'api/jobs/changeaddress',
            array(
                'job_id' => $jobId,
            ) + $address
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job');
    }

    /**
     * Allows you to assign another existing Asset to a Job by its identifier
     *
     * @param int $jobId The id of the job.
     * @param int $assetIdentifier The identifier of the asset to be assigned.
     * @param null|string|\DateTime $updateOn A Unix timestamp of the date of the asset change
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeJobAsset($jobId, $assetIdentifier, $updateOn = null)
    {
        $jobs = $this->client->post(
            'api/jobs/changeasset',
            array(
                'job_id' => $jobId,
                'asset_identifier' => $assetIdentifier,
                'updated_on' => is_null($updateOn) ? time() : (($updateOn instanceof \DateTime) ? $updateOn->format('Y-m-d H:i:s') : $updateOn)
            )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job');
    }

    /**
     * Allows you to assign another Contact person to a Job.
     * If person who do you assign job doesn't exist yet it will create new one, otherwise it will update contact entry using passed person arguments. Person arguments have prefix “person_”.
     *
     * @param integer $jobId The id of the job.
     * @param array $person Set of the person's information
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeJobPerson($jobId, $person)
    {
        $jobs = $this->client->post(
            'api/jobs/changeperson',
            array(
                'job_id' => $jobId
            ) + $person
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job');
    }

    /**
     * Allows you to assign another Customer to a Job.
     * If customer who do you assign job doesn't exist yet it will create new one, otherwise it will update customer entry using passed person arguments. Customer arguments have prefix “customer_”.
     *
     * @param integer $jobId The id of the job.
     * @param array $customer Set of the customer's information
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeJobCustomer($jobId, $customer)
    {
        $jobs = $this->client->post(
            'api/jobs/changecustomer',
            array(
                'job_id' => $jobId
            ) + $customer
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job');
    }

    /**
     * Allows you to change the Status, Address, Asset, Contact and Customer in one go.
     *
     * @param int $jobId The id of the job.
     * @param int $jobStatusId The ID of the job status to set the job to.
     * @param int $addressId The id of the address to be assigned.
     * @param int $assetIdentifier The identifier of the asset to be assigned.
     * @param int $personId The identifier of the contact person to be assigned.
     * @param int $customerId The identifier of the customer entry to be assigned.
     * @param null|string|\DateTime $updatedOn A Unix timestamp of the date of the asset change
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeJobAll($jobId, $jobStatusId, $addressId, $assetIdentifier, $personId, $customerId, $updatedOn = null)
    {
        $jobs = $this->client->post(
            'api/jobs/changeall',
            array(
                'job_id' => $jobId,
                'job_status_id' => $jobStatusId,
                'address_id' => $addressId,
                'asset_identifier' => $assetIdentifier,
                'person_id' => $personId,
                'customer_id' => $customerId,
                'updated_on' => is_null($updatedOn) ? time() : (($updatedOn instanceof \DateTime) ? $updatedOn->format('Y-m-d H:i:s') : $updatedOn)
            )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job');
    }

    /**
     * Returns a detailed list of Jobs ordered by completed and synced date time
     * 
     * @param int|null $justJobIds {optional} If set to 1 will just return job ids, otherwise it will return job ids and completed and synced date time
     * @param \DateTime $syncCompleteDateTime {optional} Date Time (YYYY-mm-dd HH:mi:ss), if set will show records greater than or equal to the sync and complete date time
     * @param int $page {optional} The current page for the result set, defaults to 1
     * @param int $limit {optional} The total number of job returned, defaults to 50
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobsOrderedBySyncAndComplete($justJobIds = null, $syncCompleteDateTime = null, $page = 1, $limit = 50)
    {
        $jobs = $this->client->get(
            'api/jobsearch/orderedsyncandcomplete',
            array(
                'just_job_ids' => $justJobIds,
                'sync_complete_date_time' => ($syncCompleteDateTime instanceof \DateTime) ? $syncCompleteDateTime->format('Y-m-d H:i:s') : '',
                'page' => $page,
                'limit' => $limit
            )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'jobs');
    }

    /**
     * Allows you to update a job workflow step
     *
     * @param int $jobId The id of the job.
     * @param int $templateWorkflowId The id of the template workflow field
     * @param null|\DateTime $doneAt The unix time the step was done at. Defaults to server time
     * @param null $actionValueEntered The data entered for the step.
     * @param null|bool $forceJobResync A boolean value (true/false, 1/0) to indicate if the job updated_on field should be set to current server time to force it to be re-synced at the next mobile sync event. Defaults to false.
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateJobWorkflow($jobId, $templateWorkflowId, $doneAt = null, $actionValueEntered = null, $forceJobResync = false)
    {
        $jobs = $this->client->post(
            'api/jobworkflows/update',
            array(
                'job_id' => $jobId,
                'template_workflow_id' => $templateWorkflowId,
                'done_at' => ($doneAt instanceof \DateTime) ? $doneAt->format('Y-m-d H:i:s') : time(),
                'action_value_entered' => $actionValueEntered,
                'force_job_resync' => $forceJobResync
            )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job_workflow');
    }

    /**
     * Allows you to update a job workflow file
     *
     * @param int $jobId The id of the job.
     * @param int $templateWorkflowId The id of the template workflow field
     * @param array $file2Upload Contains the file uploaded. Max is 8MB
     * @param null|\DateTime $doneAt The unix time the step was done at. Defaults to server time
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateJobWorkflowFile($jobId, $templateWorkflowId, $file2Upload, $doneAt = null)
    {
        $jobs = $this->client->post(
            'api/jobworkflows/file',
            array(
                'job_id' => $jobId,
                'template_workflow_id' => $templateWorkflowId,
                'file2upload' => $file2Upload,
                'done_at' => ($doneAt instanceof \DateTime) ? $doneAt->format('Y-m-d H:i:s') : time()
            )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job_workflow');
    }

    /**
     * Allows you to update a job field
     *
     * @param int $jobId The id of the job.
     * @param int $templateFieldId The id of the template field
     * @param null|\DateTime $doneAt The unix time the step was done at. Defaults to server time
     * @param null $actionValueEntered The data entered for the step.
     * @param null|bool $forceJobResync A boolean value (true/false, 1/0) to indicate if the job updated_on field should be set to current server time to force it to be re-synced at the next mobile sync event. Defaults to false.
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateJobField($jobId, $templateFieldId, $doneAt = null, $actionValueEntered = null, $forceJobResync = false)
    {
        $jobs = $this->client->post(
            'api/jobfields/update',
            array(
                'job_id' => $jobId,
                'template_field_id' => $templateFieldId,
                'done_at' => ($doneAt instanceof \DateTime) ? $doneAt->format('Y-m-d H:i:s') : time(),
                'action_value_entered' => $actionValueEntered,
                'force_job_resync' => $forceJobResync
            )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job_field');
    }

    /**
     * Allows you to update a job field file
     *
     * @param int $jobId The id of the job.
     * @param int $templateFieldId The id of the template field
     * @param array $file2Upload Contains the file uploaded. Max is 8MB
     * @param null|\DateTime $doneAt The unix time the step was done at. Defaults to server time
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateJobFieldFile($jobId, $templateFieldId, $file2Upload, $doneAt = null)
    {
        $jobs = $this->client->post(
            'api/jobfields/file',
            array(
                'job_id' => $jobId,
                'template_field_id' => $templateFieldId,
                'file2upload' => $file2Upload,
                'done_at' => ($doneAt instanceof \DateTime) ? $doneAt->format('Y-m-d H:i:s') : time()
            )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'job_field');
    }

    /**
     * Retrieves the standard excel report
     *
     * @param int $jobId The id of the job.
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobReportStandardExcel($jobId)
    {
        $jobs = $this->client->get('api/jobreports/standardexcel',  array('job_id' => $jobId))->json();
        return $this->checkPropertyAndReturn($jobs, 'template_report_id');
    }

    /**
     * Retrieves the standard PDF report
     *
     * @param int $jobId The id of the job.
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobReportStandardPDF($jobId)
    {
        $jobs = $this->client->get('api/jobreports/standardpdf',  array('job_id' => $jobId))->json();
        return $this->checkPropertyAndReturn($jobs, 'template_report_id');
    }

    /**
     * Retrieves a custom report in PDF format, the PDF is not returned in the response data,
     * instead the PDF document is sent to the callback_url once generated.
     *
     * @param int $jobId The id of the job.
     * @param int $templateReportId The id of the custom report to use
     * @param string $callbackUrl A URL to send the generated PDF after it has been created
     * @param array $callbackParams {optional} Additional parameters to add to the callback
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobReportCustomPDF($jobId, $templateReportId, $callbackUrl, $callbackParams = array())
    {
        $jobs = $this->client->post('api/jobreports/singlecustompdf',
            array(
                'job_id' => $jobId,
                'template_report_id' => $templateReportId,
                'callback_url' => $callbackUrl,
                'callback_params' => $callbackParams
            )
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'template_report_id');
    }

    /**
     * Retrieves the standard jobs report in format specified.
     * You must provide the job_template_id and either job_ids or a date range (date_from and date_to)
     *
     * @param int $jobTemplateId The id of the job template to generate the job report on
     * @param array $params Set of the information about the report
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobReportStandardJob($jobTemplateId, $params)
    {
        $jobs = $this->client->get('api/jobreports/standardjobs',
            array(
                'job_template_id' => $jobTemplateId
            ) + $params
        )->json();
        return $this->checkPropertyAndReturn($jobs, 'standard_jobs');
    }

    /**
     * Retrieves the standard jobs overview report in format specified
     * You must provide the job_ids or a date range (date_from and date_to)
     *
     * @param array $params Set of the information about the report
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobReportStandardJobOverview($params)
    {
        $jobs = $this->client->get('api/jobreports/standardjobsoverview', $params)->json();
        return $this->checkPropertyAndReturn($jobs, 'standard_jobs_overview');
    }

    /**
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEmployeesList()
    {
        $employees = $this->client->get('api/employees/all')->json();
        return $this->checkPropertyAndReturn($employees, 'employees');
    }

    /**
     * Gets job templates
     *
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobTemplates()
    {
        $jobTemplates = $this->client->get('api/jobtemplates/all')->json();
        return $this->checkPropertyAndReturn($jobTemplates, 'job_templates');
    }

    /**
     * Gets job template by id
     *
     * @param int $templateId The ID of the target job template
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobTemplateById($templateId)
    {
        $jobTemplates = $this->client->get('api/jobtemplates/get', array('template_id' => $templateId))->json();
        return $this->checkPropertyAndReturn($jobTemplates, 'job_template');
    }

    /**
     * Creates an employee
     *
     * @param $username
     * @param $password
     * @param $identifier
     * @param $email
     * @param $mobileNumber
     * @param $firstName
     * @param $lastName
     * @param bool $mobileEmployee
     * @param bool $webEmployee
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createEmployee(
        $username,
        $password,
        $identifier,
        $email,
        $mobileNumber,
        $firstName,
        $lastName,
        $mobileEmployee = true,
        $webEmployee = false
    ) {
        $employee = $this->client->post(
            'api/employees/create',
            array(
                'username' => $username,
                'password' => $password,
                'identifier' => $identifier,
                'email' => $email,
                'mobile_number' => $mobileNumber,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'mobile_user' => $mobileEmployee,
                'web_user' => $webEmployee
            )
        )->json();
        return $this->checkPropertyAndReturn($employee, 'employee_data');
    }

    /**
     * Gets all assets
     *
     * @param int $limit
     * @param int $page
     * @param null $updatedOn
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllAssets($limit = 10, $page = 0, $updatedOn = null)
    {
        $params = array('limit' => $limit, 'page' => $page);

        if (!is_null($updatedOn) && is_numeric($updatedOn)) {
            $params['updatedOn'] = intval($updatedOn);
        }

        $assets = $this->client->get('api/assets/getall', $params)->json();
        return $this->checkPropertyAndReturn($assets, 'assets');
    }

    /**
     * Gets an asset by identifier
     *
     * @param $identifier
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAssetByIdentifier($identifier)
    {
        $asset = $this->client->get(
            'api/assets/getbyidentifier',
            array(
                'asset_identifier' => $identifier
            )
        )->json();
        return $this->checkPropertyAndReturn($asset, 'asset');
    }

    /**
     * @param $identifier
     * @param $name
     * @param $assetTemplateId
     * @param $assetStatusId
     * @param $addressLine1
     * @param $addressLine2
     * @param $addressLine3
     * @param $addressCity
     * @param $addressPostalCode
     * @param $addressLat
     * @param $addressLng
     * @param array $params
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function replaceAsset(
        $identifier,
        $name,
        $assetTemplateId,
        $assetStatusId,
        $addressLine1,
        $addressLine2,
        $addressLine3,
        $addressCity,
        $addressPostalCode,
        $addressLat,
        $addressLng,
        $params = array()
    ) {
        $employee = $this->client->post(
            'api/assets/replace',
            array_merge(
                array(
                    'asset_identifier' => $identifier,
                    'asset_name' => $name,
                    'asset_template_id' => $assetTemplateId,
                    'asset_company_status_id' => $assetStatusId,
                    'address_line_1' => $addressLine1,
                    'address_line_2' => $addressLine2,
                    'address_line_3' => $addressLine3,
                    'address_city' => $addressCity,
                    'address_postal_code' => $addressPostalCode,
                    'address_lat' => $addressLat,
                    'address_lng' => $addressLng,
                    'updated_on' => time(),
                    'created_on' => time(),
                    'address_updated_on' => time()
                ),
                $params
            )
        )->json();
        return $this->checkPropertyAndReturn($employee, 'asset');
    }

    /**
     * @param $identifier
     * @param string $name
     * @param string $customerTypeName
     * @param string $industry
     * @param string $annualRevenue
     * @param string $email
     * @param string $emailAlternate
     * @param string $fax
     * @param string $phoneOffice
     * @param string $phoneAlternate
     * @param string $website
     * @param string $employees
     * @param bool $isDeleted
     * @param array $address
     * @param array $customerExtraFields
     * @param array $customerFields
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function replaceCustomer(
        $identifier,
        $name = '',
        $customerTypeName = '',
        $industry = '',
        $annualRevenue = '',
        $email = '',
        $emailAlternate = '',
        $fax = '',
        $phoneOffice = '',
        $phoneAlternate = '',
        $website = '',
        $employees = '',
        $isDeleted = false,
        $address = array(),
        $customerExtraFields = array(),
        $customerFields = array()
    ) {
        $customer = $this->client->post(
            'api/customers/replace',
            array(
                'identifier' => $identifier,
                'name' => $name,
                'customer_type_name' => $customerTypeName,
                'industry' => $industry,
                'annual_revenue' => $annualRevenue,
                'email' => $email,
                'email_alternate' => $emailAlternate,
                'fax' => $fax,
                'phone_office' => $phoneOffice,
                'phone_alternate' => $phoneAlternate,
                'website' => $website,
                'employees' => $employees,
                'is_deleted' => $isDeleted,
                'address' => $address,
                'customer_extra_fields' => $customerExtraFields,
                'customer_fields' => $customerFields
            )
        )->json();
        return $this->checkPropertyAndReturn($customer, 'customer');
    }

    /**
     * @param $identifier
     * @param $firstName
     * @param $lastName
     * @param string $email
     * @param string $emailAlternate
     * @param string $faxNumber
     * @param string $phoneNumber
     * @param string $phoneNumberAlternate
     * @param string $mobileNumber
     * @param string $personTypeName
     * @param string $personJobTitleName
     * @param string $personDepartmentName
     * @param bool $isDeleted
     * @param array $params
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function replacePerson(
        $identifier,
        $firstName,
        $lastName,
        $email = '',
        $emailAlternate = '',
        $faxNumber = '',
        $phoneNumber = '',
        $phoneNumberAlternate = '',
        $mobileNumber = '',
        $personTypeName = '',
        $personJobTitleName = '',
        $personDepartmentName = '',
        $isDeleted = false,
        $params = array()
    ) {
        $contact = $this->client->post(
            'api/people/replace',
            array_merge(
                array(
                    'identifier' => $identifier,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'email_alternate' => $emailAlternate,
                    'fax_number' => $faxNumber,
                    'phone_number' => $phoneNumber,
                    'phone_number_alternate' => $phoneNumberAlternate,
                    'mobile_number' => $mobileNumber,
                    'person_type_name' => $personTypeName,
                    'person_job_title_name' => $personJobTitleName,
                    'person_department_name' => $personDepartmentName,
                    'is_deleted' => $isDeleted
                ),
                $params
            )
        )->json();
        return $this->checkPropertyAndReturn($contact, 'person');
    }

    /**
     * Allows for changing the current status of a job.
     *
     * Valid status id's are:
     * 2 for “Rejected”
     * 3 for “Completed”
     * 4 for “Deleted”
     * 5 for “In Progress”
     * 6 for “Accepted”
     * 7 for “Incomplete”
     * 11 for “Cancelled”
     *
     * @param int $jobId The ID of the target job
     * @param int $newStatusId The ID of the job status to set the job to.
     * @param string $message An optional message to include in the job notes
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateJobStatus($jobId, $newStatusId, $message = '')
    {
        $job = $this->client->post(
            'api/jobs/status',
            array(
                'job_id' => $jobId,
                'job_status_id' => $newStatusId,
                'message' => $message,
                'updated_on' => time()
            )
        )->json();
        return $this->checkPropertyAndReturn($job, 'job');
    }

    /**
     * Allows for planning a job to an employee.
     * Planned jobs are not sent to field workers but can appear on the planner screen
     *
     * @param int $jobId The ID of the target job
     * @param int $employeeId The ID of the employee to assign the job to
     * @param \DateTime $startDateTime Start date and time for the job
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function planJob($jobId, $employeeId, $startDateTime){
        $job = $this->client->post(
            'api/jobs/plan',
            array(
                'job_id' => $jobId,
                'employee_id' => $employeeId,
                'start_date_time' => $startDateTime->format('Y-m-d H:i:s')
            )
        )->json();
        return $this->checkPropertyAndReturn($job, 'job');
    }

    /**
     * Finds an employee based on her username and password
     *
     * @param $username
     * @param $password
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEmployeeByCredentials($username, $password)
    {
        $employee = $this->client->post(
            'api/employees/getbycredentials',
            array(
                'username' => $username,
                'password' => $password
            )
        )->json();
        return $this->checkPropertyAndReturn($employee, 'employee_data');
    }

    /**
     * Updates details of an employee
     *
     * @param $id
     * @param $username
     * @param $password
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param array $params
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateEmployeeById($id, $username, $password, $firstName, $lastName, $email, $params = array())
    {
        $employee = $this->client->post(
            'api/employees/update',
            array_merge(
                array(
                    'id' => $id,
                    'username' => $username,
                    'password' => $password,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email
                ),
                $params
            )
        )->json();
        return $this->checkPropertyAndReturn($employee, 'employee_data');
    }

    /**
     * Updates details of an employee based on her identifier
     *
     * @param $identifier
     * @param $username
     * @param $password
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param array $params
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateEmployeeByIdentifier(
        $identifier,
        $username,
        $password,
        $firstName,
        $lastName,
        $email,
        $params = array()
    ) {
        $employee = $this->client->post(
            'api/employees/updatebyidentifier',
            array_merge(
                array(
                    'identifier' => $identifier,
                    'username' => $username,
                    'password' => $password,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email
                ),
                $params
            )
        )->json();
        return $this->checkPropertyAndReturn($employee, 'employee_data');
    }

    /**
     * Gets a list of company files
     *
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCompanyFiles()
    {
        $companyFileUploadResponse = $this->client->get(
            'api/companyfiles/all',
            array()
        )->json();
        return $this->checkPropertyAndReturn($companyFileUploadResponse, 'company_file_uploads');
    }

    /**
     * Gets a company file by id
     *
     * @param $companyFileId
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCompanyFile($companyFileId)
    {
        $companyFileUploadResponse = $this->client->get(
            'api/companyfile/get',
            array(
                'id' => $companyFileId
            )
        )->json();

        return $this->checkPropertyAndReturn($companyFileUploadResponse, 'company_file_upload');
    }

    /**
     * Add Company file
     *
     * @param $fileName
     * @param $fileCategory
     * @param $file
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addCompanyFile($fileName, $fileCategory, $file)
    {
        $companyFileUploadResponse = $this->client->post(
            'api/companyfile/create',
            array(
                'file_name' => $fileName,
                'file_category' => $fileCategory,
            ),
            $file
        )->json();
        return $this->checkPropertyAndReturn($companyFileUploadResponse, 'company_file_upload');
    }

    /**
     * Update a company file by ID
     *
     * @param $fileId
     * @param null $newFileName
     * @param null $newFileCategory
     * @param null $newFile
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateCompanyFile($fileId, $newFileName = null, $newFileCategory = null, $newFile = null)
    {
        $companyFileUploadResponse = $this->client->post(
            'api/companyfile/update',
            array(
                'id' => $fileId,
                'file_name' => $newFileName,
                'file_category' => $newFileCategory
            ),
            $newFile
        )->json();
        return $this->checkPropertyAndReturn($companyFileUploadResponse, 'company_file_upload');
    }

    /**
     * Delete a company file by ID
     *
     * @param $fileId
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteCompanyFile($fileId)
    {
        $companyFileUploadResponse = $this->client->post(
            'api/companyfile/delete',
            array('id' => $fileId)
        )->json();
        return $this->checkPropertyAndReturn($companyFileUploadResponse, 'company_file_upload');
    }

    /**
     * Download a company file by ID
     *
     * @param $fileId
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadCompanyFile($fileId)
    {
        $companyFileUploadResponse = $this->client->get(
            'api/companyfile/download',
            array('id' => $fileId)
        );

        return $companyFileUploadResponse;
    }

    /**
     * Gets a list of teams
     *
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTeams()
    {
        $teamResponse = $this->client->get(
            'api/teams/all',
            array()
        )->json();
        return $this->checkPropertyAndReturn($teamResponse, 'teams');
    }

    /**
     * Get a team by its id
     *
     * @param $teamId
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTeam($teamId)
    {
        $teamResponse = $this->client->get(
            'api/team/get',
            array('id' => $teamId)
        )->json();
        return $this->checkPropertyAndReturn($teamResponse, 'teams');
    }

    /**
     * Add a team
     *
     * @param $teamName
     * @param $quickTemplateId
     * @param $employeeIds
     * @param $templateIds
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addTeam($teamName, $quickTemplateId, $employeeIds, $templateIds)
    {
        $teamResponse = $this->client->post(
            'api/team/create',
            array(
                'name' => $teamName,
                'job_template_id' => $quickTemplateId,
                'employees' => $employeeIds,
                'job_templates' => $templateIds,
            )
        )->json();
        return $this->checkPropertyAndReturn($teamResponse, 'teams');
    }

    /**
     * Update a team by id
     *
     * @param $teamId
     * @param $teamName
     * @param $quickTemplateId
     * @param $employeeIds
     * @param $templateIds
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateTeam($teamId, $teamName, $quickTemplateId, $employeeIds, $templateIds)
    {
        $teamResponse = $this->client->post(
            'api/team/update',
            array(
                'id' => $teamId,
                'name' => $teamName,
                'job_template_id' => $quickTemplateId,
                'employees' => $employeeIds,
                'job_templates' => $templateIds,
            )
        )->json();
        return $this->checkPropertyAndReturn($teamResponse, 'teams');
    }

   /**
     * Delete a team by id
     *
     * @param $teamId
     * @return mixed
     * @throws GeopalException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteTeam($teamId)
    {
        $teamResponse = $this->client->post(
            'api/team/delete',
            array('id' => $teamId)
        )->json();
        return $this->checkPropertyAndReturn($teamResponse, 'teams');
    }
}
