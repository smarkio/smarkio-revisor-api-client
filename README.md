#Smark.io Revisor - API
=========================

An accelerator to communicate with © Smark.io Feedback API

Installation and usage with Composer
----------


Add the following to your composer.json file in order to fetch the latest stable version of the project:

```
{
    "require": {
        "smarkio/smarkio-revisor": "*"
    }
}
```

Then, in order to use the accelerator on your own PHP file, add the following:

```
require '[COMPOSER_VENDOR_PATH]/autoload.php';
```


Contents
--------

- src/Smarkio/Revisor - Code to interact with the Smarkio Feedback API.
- examples/ - Some examples on how to use this accelerator.

Before you start
----------------

You need to obtain one API token to use the API. This token enables the API to recognize the Revisor trying to send requests.


# Usage

## Send Lead Feedback

```
$api_token = 'YOUR API TOKEN HERE';
$lead_id = '123456789';
$status = 'converted';

// create Feedback based on the leadId in LeadOffice
$feedback = Feedback::createWithLeadId($api_token, $lead_id, $status);

// set Lead's optional parameters
$feedback->setInstantAt(date('Y-m-d H:i:s'));
$feedback->setDescription('Additional information about what happened');

// add Lead's optional extra fields
$feedback->addExtraFields(array('field_1'=>'value_1','field_2'=>'value_2'));

// send the Feedback
$response = $feedback->send();
```

## Send Lead Feedback without Lead ID

```
$api_token = 'YOUR API TOKEN HERE';
$external_id = '123456789';
$supplier_id = '1';
$status = 'converted';

// create Feedback with mandatory parameters
$feedback = Feedback::createWithSupplierExternalId($api_token, $supplier_id, $external_id, $status);

// send the Feedback
$response = $feedback->send();
```
