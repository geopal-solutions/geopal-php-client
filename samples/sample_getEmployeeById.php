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
 * The employee's Id
 * @var integer
 */
$employeeId = $params['employeeId'];

$result = $client->getEmployeeById($employeeId);

print_r($result);
