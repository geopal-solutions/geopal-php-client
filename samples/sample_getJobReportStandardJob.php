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
 * The ID of the target job template
 * @var integer
 */
$jobTemplateId = $params['templateId'];

/**
 * Set of the information about the report
 * @var array
 */
$params = array(
    'export_type' => 'json',
    'job_ids' => array($params['jobId']),
    'job_status_ids' => '',
    'employee_created_id' => '',
    'employee_updated_id' => '',
    'team_id' => '',
    'date_time_filter_type' => '',
    'date_from' => '',
    'date_to' => '',
    'asset_template_id' => '',
    'asset_identifier' => '',
    'customer_id' => '',
    'person_id' => '',
    'extra_fields' => '',
    'select_fields' => '',
    'stop_double_json_encode' => ''
);

$result = $client->getJobReportStandardJob($jobTemplateId, $params);

print_r($result);
