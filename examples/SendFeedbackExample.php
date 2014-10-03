<?php
/*
 * An example on how to create a Lead Feedback and how to use the API
 * to send it to LeadOffice.
 */

require __DIR__ . '/../src/Smarkio/Revisor/Feedback.php';

use Smarkio\Revisor\Feedback;

$api_token = 'INSERT YOUR TOKEN HERE';
$lead_id = '1234';
$status = 'integrated';

// create Feedback based on the leadId in LeadOffice
$feedback = Feedback::createWithLeadId($api_token, $lead_id, $status);

// set Lead's optional parameters
$feedback->setInstantAt(date('Y-m-d H:i:s'));
$feedback->setDescription('Additional information about what happened');

// add Lead's optional extra fields
$feedback->addExtraFields(array('profession'=>'developer','nationality'=>'portuguese'));

// send the Feedback
$response = $feedback->send();

echo "API Response: '{$response}'\n";
