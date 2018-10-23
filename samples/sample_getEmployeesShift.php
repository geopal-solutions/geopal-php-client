<?php
/**
 * A script to test the Geopal's api/employees/getbyidentifier
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
$dateTimeFrom = new \DateTime('2018-10-22 07:00:00');


/**
 * Date and time to list entries to.
 * @var \DateTime
 */
$dateTimeTo = new \DateTime('2018-10-24 19:30:00');

$result = $client->getEmployeesShift($dateTimeFrom, $dateTimeTo);

print_r($result);
