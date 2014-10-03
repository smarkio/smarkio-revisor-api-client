<?php
/*
 * An example on how to create a Lead Feedback
 * (with the mandatory parameters only) and how to use the API
 * to send it to Smark.io.
 */

require __DIR__ . '/../src/Smarkio/Revisor/Feedback.php';

use Adclick\Smarkio\Revisor\Feedback;

$api_token = 'INSERT YOUR TOKEN HERE';
$external_id = '3';
$supplier_id = '21';
$status = 'converted';

// create Feedback with mandatory parameters
$feedback = Feedback::createWithSupplierExternalId($api_token, $supplier_id, $external_id, $status);

// send the Feedback
$response = $feedback->send();

echo "API Response: '{$response}'\n";
