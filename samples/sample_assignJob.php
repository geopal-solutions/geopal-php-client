<?php
/**
 * A script to test the Geopal's api/jobs/assign
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
 * Start date and time for the job
 * @var \DateTime
 */
$startDateTime = $params['startDateTime'];

/**
 * The ID of the employee to assign the job to
 * @var integer
 */
$employeeId = $params['employeeId'];

$result = $client->assignJob($jobId, $startDateTime, $employeeId);

print_r($result);
