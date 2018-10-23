<?php
/**
 * A script to test the Geopal's api/jobs/changeasset
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
 * The identifier of the asset to be assigned.
 * @var integer
 */
$assetIdentifier = $params['assetIdentifier'];

$result = $client->changeJobAsset($jobId, $assetIdentifier);

print_r($result);
