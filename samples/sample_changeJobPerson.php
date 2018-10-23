<?php
/**
 * A script to test the Geopal's api/jobs/changeperson
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
 * Allows you to assign another Contact person to a Job.
 * If person who do you assign job doesn't exist yet it will create new one, otherwise it will update contact entry using passed person arguments. Person arguments have prefix “person_”.
 * IMPORTANT: either person_id or person_identifier are required
 *
 * @var array
 */
$person = [
    'person_id' => '',
    'person_identifier' => '',
    'person_first_name' => '',
    'person_last_name' => '',
    'person_title' => '',
    'person_phone_number' => '',
    'person_email' => '',
    'person_fax_number' => '',
    'person_mobile_number' => '',
    'person_address_line_1' => '',
    'person_address_line_2' => '',
    'person_address_line_3' => '',
    'person_city' => '',
    'person_postal_code' => '',
    'person_country_id' => '',
    'person_address_lat' => '',
    'person_address_lng' => '',
    'updated_on' => ''
];

$result = $client->changeJobPerson($jobId, $person);

print_r($result);
