<?php
/**
 * A script to test the Geopal's api/employees/getbyid
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

/**
 * Date and time to list entries from.
 * @var \DateTime
 */
$startOn = new \DateTime('2018-10-23 07:00:00');


/**
 * Date and time to list entries to.
 * @var \DateTime
 */
$stopOn = new \DateTime('2018-10-23 19:30:00');


$result = $client->createEmployeeShift($startOn, $stopOn);

print_r($result);
