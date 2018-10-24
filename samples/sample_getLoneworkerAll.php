<?php
/**
 * A script to test the Geopal's api/loneworker/distributionlists/getall
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

$result = $client->getLoneworkerAll();

print_r($result);
