<?php
/**
 * A script to test the Geopal's api/jobsearch/ids
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);


/**
 * Date and time to list entries from.
 * @var string
 */
//$dateTimeFrom = '2018-10-01 00:00:00';
$dateTimeFrom = new \DateTime();
$dateTimeFrom->sub(new DateInterval('P1D'));

/**
 * Date and time to list entries to.
 * @var string
 */
//$dateTimeTo = '2018-10-30 00:00:00';
$dateTimeTo = new \DateTime();
$dateTimeTo->add(new DateInterval('P1D'));


$result = $client->getJobsBetweenDateRange($dateTimeFrom, $dateTimeTo);

print_r($result);
