<?php
/**
 * A script to test the Geopal's api/employees/all
 *
 * @author Mark McCullagh <mark.mccullagh@geopal-solutions.com>
 * Date: 07/06/16
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

/**
 * Job Id
 *
 * @var integer
 */
$params = 17915958;

$result = $client->getJobById($params);

print_r($result);
