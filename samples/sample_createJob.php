<?php
/**
 * A script to test the Geopal's api/jobs/create
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

/**
 * The ID of the job template to create the job from
 * @var integer
 */
$templateId = $params['templateId'];

/**
 * Set of information for job creation
 * @var array
 */
$params = array();

$result = $client->createJob($templateId, $params);

print_r($result);
