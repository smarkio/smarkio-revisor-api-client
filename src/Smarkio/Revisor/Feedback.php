<?php
/**
 *
 *
 * @author     Vítor Santos <vitor.santos@smark.io>
 * @copyright  2014 Smark.io
 * @license    http://opensource.org/licenses/MIT MIT License
 *
 */

namespace Smarkio\Revisor;

require_once('SendFeedback.php');

class Feedback
{

    // Feedback fields array
    private $feedbackFields = array();

    // Extra fields array
    private $extraFields = array();

    private $api_token = null;

    public function __construct($api_token = null)
    {

        if (!is_null($api_token))
        {
            $this->setApiToken($api_token);
        }
    }

    /**
     * Sets the API access token
     *
     * @param string $api_token
     */
    public function setApiToken($api_token)
    {
        $this->api_token = $api_token;
    }

    /**
     * @return null
     */
    public function getApiToken()
    {
        return $this->api_token;
    }

    /**
     * Creates a new instance of Feedback using Smark.io leadId as lead key
     *
     * @param $api_token string your API token
     * @param $leadId string|int the identifier of the lead in Smark.io
     * @param $status string the status of the lead
     * @return Feedback
     */
    public static function createWithLeadId($api_token, $leadId, $status)
    {
        $feedback = new self($api_token);
        $feedback->setLeadId($leadId);
        $feedback->setStatus($status);
        return $feedback;
    }

    /**
     * Creates a new instance of Feedback using Smark.io supplierId+externalId as lead key
     *
     * @param $api_token string your API token
     * @param $supplierId string|int the identifier of the supplier in Smark.io
     * @param $externalId string|int the identifier of the lead for the supplier.
     * @param $status string the status of the lead
     * @return Feedback
     */
    public static function createWithSupplierExternalId($api_token, $supplierId, $externalId, $status)
    {
        $feedback = new self($api_token);
        $feedback->setSupplierId($supplierId);
        $feedback->setExternalId($externalId);
        $feedback->setStatus($status);
        return $feedback;
    }

    /**
     * Returns the lead id, if set
     *
     * @return int|string|null The lead id
     */
    public function getLeadId()
    {
        return isset($this->feedbackFields['lead_id']) ? $this->feedbackFields['lead_id'] : null;
    }

    /**
     * Sets the lead id
     *
     * @param $leadId int|string The lead id
     */
    public function setLeadId($leadId)
    {
        $this->feedbackFields['id'] = $leadId;
    }

    /**
     * Returns the supplier id, if set
     *
     * @return int|string|null The supplier id
     */
    public function getSupplierId()
    {
        return isset($this->feedbackFields['supplier_id']) ? $this->feedbackFields['supplier_id'] : null;
    }

    /**
     * Sets the supplier id
     *
     * @param $supplierId int|string The supplier id
     */
    public function setSupplierId($supplierId)
    {
        $this->feedbackFields['supplier_id'] = $supplierId;
    }

    /**
     * Returns the lead external id, if set
     *
     * @return int|string|null The lead external id
     */
    public function getExternalId()
    {
        return isset($this->feedbackFields['external_id']) ? $this->feedbackFields['external_id'] : null;
    }

    /**
     * Sets the lead external id
     *
     * @param $externalId int|string The lead external id
     */
    public function setExternalId($externalId)
    {
        $this->feedbackFields['external_id'] = $externalId;
    }

    /**
     * Returns the feedback instant, if set
     *
     * @return string|null The feedback instant
     */
    public function getInstantAt()
    {
        return isset($this->feedbackFields['instant_at']) ? $this->feedbackFields['instant_at'] : null;
    }

    /**
     * Sets the feedback instant
     *
     * @param $instantAt string The feedback instant
     */
    public function setInstantAt($instantAt)
    {
        $this->feedbackFields['instant_at'] = $instantAt;
    }

    /**
     * Returns the feedback status, if set
     *
     * @return string The feedback instant
     */
    public function getStatus()
    {
        return isset($this->feedbackFields['status']) ? $this->feedbackFields['status'] : null;
    }

    /**
     * Sets the feedback status
     *
     * @param $status string The feedback status
     */
    public function setStatus($status)
    {
        $this->feedbackFields['status'] = $status;
    }

    /**
     * Returns the feedback description, if set
     *
     * @return string The feedback description
     */
    public function getDescription()
    {
        return isset($this->feedbackFields['description']) ? $this->feedbackFields['description'] : null;
    }

    /**
     * Sets the feedback description
     *
     * @param $description string The feedback description
     */
    public function setDescription($description)
    {
        $this->feedbackFields['description'] = $description;
    }

    /**
     * Returns the feedback fields to be sent to the API, if set
     *
     * @return array The feedback fields
     */
    public function getFeedbackFields()
    {
        return $this->feedbackFields;
    }

    /**
     * Returns the feedback extra fields to be sent to the API, if set
     *
     * @return array The feedback extra fields
     */
    public function getExtraFields()
    {
        return isset($this->extraFields) ? $this->extraFields : null;
    }

    /**
     * Adds an extra field
     *
     * @param $field string The extra field key
     * @param $value string The extra field value
     */
    public function addExtraField($field,$value){
        $this->extraFields[$field] = $value;
    }

    /**
     * Adds multiple extra fields
     *
     * @param $extra_fields array The extra fields as an array of the format { key1 => value1, key2 => value2, ... }
     */
    public function addExtraFields($extra_fields){
        if(!isset($this->extraFields)){
            $this->extraFields = array();
        }
        $this->extraFields = array_merge($this->extraFields,$extra_fields);
    }

    /**
     * Sends this lead feedback to Smark.io using Smark.io API.
     *
     * @param null|string $api_base_url API base url. If null, default is used.
     *
     * @return mixed
     */
    public function send($api_base_url = null)
    {
        $sendFields = array('lead' => $this->getFeedbackFields());

        if (isset($this->extraFields) && (count($this->extraFields) > 0))
        {
            $sendFields['extra'] = $this->extraFields;
        }

        return SendFeedback::send($this->api_token, $sendFields, $api_base_url);
    }
}