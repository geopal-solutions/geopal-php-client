<?php
/**
 * A script to test the Geopal's api/jobs/getbyidentifier
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
$jobId = 17915958;

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
$jobStatusId = 17915958;

/**
 * An optional message to include in the job notes
 * @var string
 */
$message = "";

$result = $client->updateJobStatus($jobId, $jobStatusId, $message);

print_r($result);
