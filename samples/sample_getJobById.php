<?php
/**
 * A script to test the Geopal's api/jobs/get
 *
 * @author Tarini Coll
 * Date: 22/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

/**
 * Job Id
 * @var integer
 */
$params = 17915958;

$result = $client->getJobById($params);

print_r($result);
