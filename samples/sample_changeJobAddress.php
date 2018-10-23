<?php
/**
 * A script to test the Geopal's api/jobs/changeaddress
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

/**
 * The ID of the target job
 * @var integer
 */
$jobId = $params['jobId'];

/**
 * If passed doesn't exist yet it will create new one otherwise it will update contact entry using passed address arguments. Address arguments have prefix “address_”.
 * IMPORTANT: Either address_id or address_identifier are required.
 *
 * @var array
 */
$address = [
    'address_id' => '',
    'address_identifier' => '',
    'address_line_1' => '',
    'address_line_2' => '',
    'address_line_3' => '',
    'address_city' => '',
    'address_postal_code' => '',
    'address_country_code' => '',
    'updated_on' => ''
];

$result = $client->changeJobAddress($jobId, $address);

print_r($result);
