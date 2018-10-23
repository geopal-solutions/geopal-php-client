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
 * Params to create an employee
 */
$username = 'johndoe';
$password = 'passwd1';
$email = 'tarini.coll@geopal-solutions.com';
$firstName = 'John';
$lastName = 'Doe';

$result = $client->createEmployee($username, $password, $email, $firstName, $lastName);

print_r($result);
