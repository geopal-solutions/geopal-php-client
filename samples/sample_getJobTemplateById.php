<?php
/**
 * A script to test the Geopal's api/jobtemplates/get
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

/**
 * The ID of the target job template
 * @var integer
 */
$templateId = $params['templateId'];


$result = $client->getJobTemplateById($templateId);

print_r($result);
