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
 * The ID of the employee to assign the job to
 * @var integer
 */
$employeeId = 17915958;

/**
 * Start date and time for the job
 * @var \Date
 */
$startDateTime = new \Date();

$result = $client->planJob($jobId, $employeeId, $startDateTime);

print_r($result);
