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

$result = $client->getEmployeesList();
print_r($result);
