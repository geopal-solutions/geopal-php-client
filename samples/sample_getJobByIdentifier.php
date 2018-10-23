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
 * Job Identifier
 * @var integer
 */
$params = 1;

$result = $client->getJobByIdentifier($params);

print_r($result);
