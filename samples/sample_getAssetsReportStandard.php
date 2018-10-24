<?php
/**
 * A script to test the Geopal's api/jobreports/standardjobs
 *
 * @author Tarini Coll
 * Date: 23/10/2018
 */
require '../vendor/autoload.php';
require 'settings.php';

$client = new \Geopal\Geopal($employeeId, $privateKey);

/**
 * Set of the information about the report
 * @var array
 */
$params = array(
    'export_type' => 'json',
    'asset_identifiers' => array(),
    'asset_company_status_ids' => '',
    'asset_category_id' => '',
    'asset_subcategory_id' => '',
    'asset_template_id' => '',
    'customer_id' => ''
);

$result = $client->getAssetsReportStandard($params);

print_r($result);
