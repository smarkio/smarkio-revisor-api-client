<?php
/*
 * An example on how to create multiple Lead Feedback and how to use the API
 * to send it to Smark.io.
 */

function __autoload($class)
{
    include('..'.DIRECTORY_SEPARATOR.'src' . DIRECTORY_SEPARATOR. str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php');
}

use Smarkio\Revisor\Feedback;
use Smarkio\Revisor\FeedbackCollection;

$apiToken   = 'YOUR API TOKEN HERE';

$feedbacks = array(
    array('lead_id' => 123, 'status' => 'converted', 'instant_at' => date('Y-m-d H:i:s')),
    array('lead_id' => 124, 'status' => 'converted', 'instant_at' => date('Y-m-d H:i:s'), 'extra_attribute_1' => 'value_1')
);

$feedbacks2 = array(
    array('lead_id' => 123, 'status' => 'converted', 'instant_at' => date('Y-m-d H:i:s'), 'extra' => array()),
    array('lead_id' => 124, 'status' => 'converted', 'instant_at' => date('Y-m-d H:i:s'), 'extra' => array('extra_attribute_1' => 'value_1'))
);

// By unserializing an array
echo "<h1>Example 1</h1>\n";
try {
    $collection = new FeedbackCollection($apiToken, $feedbacks);
    $result = $collection->send();
    if ( $result['success'] )
    {
        echo $result['message']."\n";
    }
}
catch (\Smarkio\Exception\InvalidTokenException $e)
{
    echo "<h2>Invalid Token!</h2>\n";
    echo $e->getMessage()."\n";
}
catch (\Smarkio\Exception\InvalidFeedbackException $e)
{
    echo "<h2>Invalid Feedback!</h2>\n";
    echo $e->getMessage()."\n";
}
catch (\Smarkio\Exception\ServiceUnavailableException $e)
{
    echo "<h2>Service Unavailable!</h2>\n";
    echo $e->getMessage()."\n";
}


echo "<h1>Example 2</h1>\n";
try {
    $collection = new FeedbackCollection($apiToken, json_encode($feedbacks));
    $result = $collection->send();
    if ( $result['success'] )
    {
        echo $result['message']."\n";
    }
}
catch (\Smarkio\Exception\InvalidTokenException $e)
{
    echo "<h2>Invalid Token!</h2>\n";
    echo $e->getMessage()."\n";
}
catch (\Smarkio\Exception\InvalidFeedbackException $e)
{
    echo "<h2>Invalid Feedback!</h2>\n";
    echo $e->getMessage()."\n";
}
catch (\Smarkio\Exception\ServiceUnavailableException $e)
{
    echo "<h2>Service Unavailable!</h2>\n";
    echo $e->getMessage()."\n";
}

// By adding sequentialy
echo "<h1>Example 3</h1>\n";
try {
    $collection = new FeedbackCollection($apiToken);
    foreach ($feedbacks2 as $row)
    {
        $collection->createAndAddFeedback($row['lead_id'], $row['status'], $row['instant_at'], $row['extra']);
    }
    $result = $collection->send();
    if ( $result['success'] )
    {
        echo $result['message']."\n";
    }
}
catch (\Smarkio\Exception\InvalidTokenException $e)
{
    echo "<h2>Invalid Token!</h2>\n";
    echo $e->getMessage()."\n";
}
catch (\Smarkio\Exception\InvalidFeedbackException $e)
{
    echo "<h2>Invalid Feedback!</h2>\n";
    echo $e->getMessage()."\n";
}
catch (\Smarkio\Exception\ServiceUnavailableException $e)
{
    echo "<h2>Service Unavailable!</h2>\n";
    echo $e->getMessage()."\n";
}

echo "<h1>Example 4</h1>\n";
try {
    foreach ($feedbacks2 as $row)
    {
        $feedback = new Feedback();
        $feedback->setLeadId($row['lead_id']);
        $feedback->setStatus($row['status']);
        $feedback->setInstantAt($row['instant_at']);
        $feedback->addExtraFields($row['extra']);
        $collection->add($feedback);
    }
    $result = $collection->send();

    if ( $result['success'] )
    {
        echo $result['message']."\n";
    }
}
catch (\Smarkio\Exception\InvalidTokenException $e)
{
    echo "<h2>Invalid Token!</h2>\n";
    echo $e->getMessage()."\n";
}
catch (\Smarkio\Exception\InvalidFeedbackException $e)
{
    echo "<h2>Invalid Feedback!</h2>\n";
    echo $e->getMessage()."\n";
}
catch (\Smarkio\Exception\ServiceUnavailableException $e)
{
    echo "<h2>Service Unavailable!</h2>\n";
    echo $e->getMessage()."\n";
}