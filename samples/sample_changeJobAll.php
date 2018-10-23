<?php
/**
 * A script to test the Geopal's api/jobs/changeall
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

/**
 * The ID of the target job
 * @var integer
 */
$jobId = $params['jobId'];

/**
 * The ID of the job status to set the job to.
 * Valid values are:
 * 2 for “Rejected”
 * 3 for “Completed”
 * 4 for “Deleted”
 * 5 for “In Progress”
 * 6 for “Accepted”
 * 7 for “Incomplete”
 * 11 for “Cancelled”
 *
 * @var integer
 */
$jobStatusId = $params['jobStatusId'];

/**
 * The identifier of the address to be assigned
 * @var integer
 */
$addressId = $params['addressId'];

/**
 * The identifier of the asset to be assigned.
 * @var integer
 */
$assetIdentifier = $params['assetIdentifier'];

/**
 * The identifier of the person to be assigned
 * @var integer
 */
$personId = $params['personId'];

/**
 * The identifier of the customer to be assigned
 * @var integer
 */
$customerId = $params['customerId'];


$result = $client->changeJobAll($jobId, $jobStatusId, $addressId, $assetIdentifier, $personId, $customerId);

print_r($result);
