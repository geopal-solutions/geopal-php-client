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
 * The ID of the employee to assign the job to
 * @var integer
 */
$employeeId = $params['employeeId'];

/**
 * The ID of the employee to assign the job to
 * @var integer
 */
$employeeIdentifier = $params['employeeIdentifier'];

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

echo "=============== BY ID =================\n";
$resultById = $client->getEmployeeRouteReplayById($employeeId, $dateTimeFrom, $dateTimeTo);
print_r($resultById);

echo "============ BY IDENTIFIER ============\n";

$resultByIdentifier = $client->getEmployeeRouteReplayByIdentifier($employeeIdentifier, $dateTimeFrom, $dateTimeTo);
print_r($resultByIdentifier);
