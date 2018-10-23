<?php
/**
 * A script to test the Geopal's api/jobs/unassign
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

$result = $client->unassignJob($jobId);

print_r($result);
