<?php
/**
 * A script to test the Geopal's api/jobs/changecustomer
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
 * IMPORTANT: either customer_id or customer_identifier are required
 *
 * @var array
 */
$customer = [
    'customer_id' => '',
    'customer_identifier' => '',
    'customer_name' => '',
    'customer_website' => '',
    'customer_phone_fax' => '',
    'customer_phone_office' => '',
    'customer_phone_alternate' => '',
    'customer_email' => '',
    'updated_on' => ''
];

$result = $client->changeJobCustomer($jobId, $customer);

print_r($result);
