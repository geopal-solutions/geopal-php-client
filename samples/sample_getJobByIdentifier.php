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
 * The unique identifier of the target job
 * @var integer
 */
$jobIdentifier = $params['jobIdentifier'];

$result = $client->getJobByIdentifier($jobIdentifier);

print_r($result);
