<?php
namespace Geopal;

use Geopal\Http\Client;
use Geopal\Exceptions\GeopalException;
use Guzzle\Http\Message\Response;

/**
 * Class Geopal
 *
 * @package Geopal
 *
 * @method mixed createAndAssignJob($templateId, array $params = [])
 * @method mixed createJob($templateId, array $params)
 * @method mixed assignJob(\DateTime $startDateTime, $assignedToEmployeeId)
 * @method mixed reassignJob($jobId, $employeeReassignedToId, \DateTime $startDateTime)
 * @method mixed getJobById($jobId)
 * @method mixed getJobsBetweenDateRange($dateTimeFrom, $dateTimeTo)
 * @method mixed getEmployeesList()
 * @method mixed getJobTemplates()
 * @method mixed getJobTemplateById($templateId)
 * @method mixed createEmployee($username, $password, $identifier, $email, $mobileNumber, $firstName, $lastName, bool $mobileEmployee, bool $webEmployee)
 * @method mixed getAllAssets(int $limit, int $page, int $updatedOn)
 * @method mixed getAssetByIdentifier($identifier)
 * @method mixed replaceAsset($identifier, $name, $assetTemplateId, $assetStatusId, $addressLine1, $addressLine2, $addressLine3, $addressCity, $addressPostalCode, $addressLat, $addressLng, array $params)
 * @method mixed replaceCustomer(string $identifier, string $name, string $customerTypeName, string $industry, string $annualRevenue, string $email, string $emailAlternate, string $fax, string $phoneOffice, string $phoneAlternate, string $website, string $employees, bool $isDeleted, array $address, array $customerExtraFields, array $customerFields)
 * @method mixed replacePerson(string $identifier, string $firstName, string $lastName, string $email, string $emailAlternate, string $faxNumber, string $phoneNumber, string $phoneNumberAlternate, string $mobileNumber, string $personTypeName, string $personJobTitleName, string $personDepartmentName, bool $isDeleted, array $params)
 * @method mixed updateJobStatus(int $jobId, int $newStatusId, string $message)
 * @method mixed getEmployeeByCredentials(string $username, string $password)
 * @method mixed updateEmployeeById($id, $username, $password, $firstName, $lastName, $email, array $params)
 * @method mixed updateEmployeeByIdentifier($identifier, $username, $password, $firstName, $lastName, $email, array $params)
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

    protected $onlyExceptions;
    /**
     * @param $employeeId
     * @param $privateKey
     */
    public function __construct($employeeId, $privateKey, $onlyExceptions = false)
    {
        $this->attachClient($employeeId, $privateKey);
        $this->onlyExceptions = $onlyExceptions;
    }

    /**
     * @param $array
     * @param $key
     * @return array|Response
     * @throws Exceptions\GeopalException
     */
    protected function checkPropertyAndReturn($array, $key)
    {
        if (is_array($array) && array_key_exists('status', $array)) {
            if ($array['status'] == true) {
                if (array_key_exists($key, $array)) {
                    return $array[$key];
                } else {
                    // if key does not exist probably there is no data for it so return everything
                    return $array;
                }
            } else {
                throw new GeopalException($array['error_message'], $array['error_code']);
            }
        } elseif (is_array($array) && array_key_exists('status', $array)) {
            throw new GeopalException($array['error_message'], $array['error_code']);
        } else {
            throw new GeopalException('Invalid data or key not found');
        }
    }

    /**
     * @param int $employeeId
     * @param string $privateKey
     */
    public function attachClient($employeeId, $privateKey)
    {
        $this->setEmployeeId($employeeId);
        $this->setPrivateKey($privateKey);
        $this->client = new Client($this->getEmployeeId(), $this->getPrivateKey());
    }
    /**
     * @param  $client
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
     * Describes all methods implemented in the API
     *
     * @var array
     */
    private $methods = [
        'createAndAssignJob' => [
            'verb'     => 'post',
            'endpoint' => 'api/jobs/createandassign',
            'params'   => ['template_id', '_arrayParams' => []],
            'property' => 'job'
        ],
        'createJob' => [
            'verb'     => 'post',
            'endpoint' => 'api/jobs/create',
            'params'   => ['template_id', '_arrayParams' => []],
            'property' => 'job'
        ],
        'assignJob' => [
            'verb'     => 'post',
            'endpoint' => 'api/jobs/assign',
            'params'   => ['job_id', 'start_date_time' => 'datetime', 'employee_id'],
            'property' => 'job'
        ],
        'reassignJob' => [
            'verb'     => 'post',
            'endpoint' => 'api/jobs/reassign',
            'params'   => ['job_id', 'employee_reassigned_to_id', 'start_date_time' => 'datetime'],
            'property' => 'job'
        ],
        'getJobById' => [
            'verb'     => 'get',
            'endpoint' => 'api/jobs/get',
            'params'   => ['job_id'],
            'property' => 'job'
        ],
        'getJobsBetweenDateRange' => [
            'verb'     => 'get',
            'endpoint' => 'api/jobsearch/ids',
            'params'   => ['date_time_from', 'date_time_to'],
            'property' => 'jobs'
        ],
        'getEmployeesList' => [
            'verb'     => 'get',
            'endpoint' => 'api/employees/all',
            'params'   => [],
            'property' => 'employees'
        ],
        'getJobTemplates' => [
            'verb'     => 'get',
            'endpoint' => 'api/jobtemplates/all',
            'params'   => [],
            'property' => 'job_templates'
        ],
        'getJobTemplateById' => [
            'verb'     => 'get',
            'endpoint' => 'api/jobtemplates/get',
            'params'   => ['template_id'],
            'property' => 'job_template'
        ],
        'createEmployee' => [
            'verb'     => 'post',
            'endpoint' => 'api/employees/create',
            'params'   => ['username', 'password', 'identifier', 'email', 'mobile_number', 'first_name', 'last_name', 'mobile_user', 'web_user'],
            'property' => 'employee_data'
        ],
        'getAllAssets' => [
            'verb'     => 'get',
            'endpoint' => 'api/assets/getall',
            'params'   => ['limit' => 10, 'page' => 0, 'updatedOn' => 'timestamp'],
            'property' => 'assets'
        ],
        'getAssetByIdentifier' => [
            'verb'     => 'get',
            'endpoint' => 'api/assets/getbyidentifier',
            'params'   => ['asset_identifier'],
            'property' => 'asset'
        ],
        'replaceAsset' => [
            'verb'     => 'post',
            'endpoint' => 'api/assets/replace',
            'params'   => ['asset_identifier', 'asset_name', 'asset_template_id', 'asset_company_status_id', 'address_line_1', 'address_line_2',
                'address_line_3', 'address_city', 'address_postal_code', 'address_lat', 'address_lng', 'updated_on' => 'timenow',
                'created_on' => 'timenow', 'address_updated_on' => 'timenow', '_arrayParams' => []],
            'property' => 'asset'
        ],
        'replaceCustomer' => [
            'verb'     => 'post',
            'endpoint' => 'api/customers/replace',
            'params'   => ['identifier', 'name' => '', 'customer_type_name' => '', 'industry' => '', 'annual_revenue' => '',
                'email' => '', 'email_alternate' => '', 'fax' => '', 'phone_office' => '', 'phone_alternate' => '',
                'website' => '', 'employees' => '', 'is_deleted' => false, 'address' => [],
                'customer_extra_fields' => [], 'customer_fields' => []],
            'property' => 'customer'
        ],
        'replacePerson' => [
            'verb'     => 'post',
            'endpoint' => 'api/people/replace',
            'params'   => ['identifier', 'first_name', 'last_name', 'email' => '', 'email_alternate' => '', 'fax_number' => '',
                'phone_number' => '', 'phone_number_alternate' => '', 'mobile_number' => '', 'person_type_name' => '',
                'person_job_title_name' => '', 'person_department_name' => '', 'is_deleted' => false, '_arrayParams' => []],
            'property' => 'person'
        ],
        'updateJobStatus' => [
            'verb'     => 'post',
            'endpoint' => 'api/jobs/status',
            'params'   => ['job_id', 'job_status_id', 'message' => '', 'updated_on' => 'timenow'],
            'property' => 'job'
        ],
        'getEmployeeByCredentials' => [
            'verb'     => 'post',
            'endpoint' => 'api/employees/getbycredentials',
            'params'   => ['username', 'password'],
            'property' => 'employee_data'
        ],
        'updateEmployeeById' => [
            'verb'     => 'post',
            'endpoint' => 'api/employees/update',
            'params'   => ['id', 'username', 'password', 'first_name', 'last_name', 'email', '_arrayParams' => []],
            'property' => 'employee_data'
        ],
        'updateEmployeeByIdentifier' => [
            'verb'     => 'post',
            'endpoint' => 'api/employees/updatebyidentifier',
            'params'   => ['identifier', 'username', 'password', 'first_name', 'last_name', 'email', '_arrayParams' => []],
            'property' => 'employee_data'
        ]
    ];

    /**
     * @param string $method HTTP method
     * @param string $endpoint Geopal API endpoint
     * @param mixed $params
     * @return array|Response
     */
    public function apiEndpoint($method, $endpoint, $params, $subset = '_no-property_')
    {
        $response = $this->client->{$method}($endpoint, $params);
        $response = json_decode($response->getBody(), true);
        $result = $this->checkPropertyAndReturn($response, $subset);

        return $result;
    }

    /**
     * Resolves the parameters array for a method name.
     * Fills with default values or special cases (e.g. time()), triggers errors on missing mandatory parameters.
     *
     * @param string $methodName Geopal API method name
     * @param array $expected Defined parameters for the specified method name
     * @param array $provided Parameters array provided by the user (arguments)
     * @return array
     * @throws GeopalException
     */
    private function getMethodParams($methodName, $expected, $provided)
    {
        $result = [];

        foreach ($expected as $key => $defaultValue) {
            $triggerError = false;

            // attempt to guess the param name ( ['jobId'] or [0 => 'jobId'] or ['jobId' => 'someDefault')
            $paramName = !is_numeric($key) ? $key : $defaultValue;

            // get the param value, either from the $provided arguments
            if (isset($provided[ $paramName ])) {
                $paramValue = $provided[ $paramName ];
            } else {
                // no value received, maybe $defaultValue is actually the default value and not the param name
                if (is_numeric($key)) {
                    $triggerError = true;
                } else {
                    $paramValue = $defaultValue;
                }
            }

            // has a default value or special context
            if (!is_numeric($key)) {
                $paramName = $key;

                switch (true) {
                    // to fetch the current time() value
                    // for DateTime parameters
                    case 'datetime' === $defaultValue:
                        if (!$triggerError) {
                            $result[$paramName] = $paramValue->format('Y-m-d H:i:s');
                        }
                        break;
                    // for UNIX timestamp parameters
                    case 'timestamp' === $defaultValue:
                        if (!$triggerError) {
                            if (!is_null($paramValue) && is_numeric($paramValue)) {
                                $result[$paramName] = intval($paramValue);
                            }
                        }
                        break;
                    case 'timenow' === $defaultValue:
                        $result[$paramName] = time();
                        break;
                    default :
                        $result[$paramName] = $paramValue;
                        break;
                }
            } else {
                $paramName = $defaultValue;

                // $defaultValue is the actual param name, in this case
                if (!$triggerError) {
                    $result[$paramName] = $paramValue;
                }
            }
            if ($triggerError) {
                $trace = debug_backtrace();
                $caller = next($trace);
                if (!$this->onlyExceptions) {
                    trigger_error(sprintf("Missing argument `%s` for %s() called in %s on line %s", $paramName, $methodName, $caller['file'], $caller['line']), E_USER_WARNING);
                } else {
                    throw new GeopalException(sprintf("Missing argument `%s` for %s()", $paramName, $methodName));
                }
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws GeopalException
     */
    public function __call($name, $arguments)
    {
        if (0 == count($arguments)) {
            $arguments[0] = [];
        }
        if (!isset($this->methods[$name])) {
            throw new GeopalException("API method `$name` is not implemented.");
        }

        $method = $this->methods[$name];

        $params = $this->getMethodParams($name, $method['params'], $arguments[0]);

        if (isset($params['_arrayParams'])) {
            $arrayParams = $params['_arrayParams'];

            unset($params['_arrayParams']);
            $params = array_merge($params, $arrayParams);
        }
        /**
         * @var \GuzzleHttp\Psr7\Response $response
         */
        $response = $this->client->{$method['verb']}($method['endpoint'], $params);
        $response = json_decode($response->getBody(, true);
        $result = $this->checkPropertyAndReturn($response, $method['property']);

        return $result;
    }
}
