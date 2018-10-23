<?php
/**
 * A script to test the Geopal's api/teams/all
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

$result = $client->getTeams();

print_r($result);
