<?php
/**
 * Created by PhpStorm.
 * User: ivorodrigues
 * Date: 31/03/17
 * Time: 12:41
 */

namespace Smarkio\Revisor;


use Smarkio\Exception\InvalidFeedbackException;
use Smarkio\Exception\InvalidTokenException;
use Smarkio\Exception\ServiceUnavailableException;

class FeedbackCollection implements \Iterator
{
    const API_BASE_URL = 'https://api.smark.io/';

    /**
     * @var string
     */
    protected $token = '';

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * @var Feedback[]
     */
    protected $collection = array();

    /**
     * FeedbackCollection constructor.
     *
     * @param $token
     * @param null|string|array $data
     * @throws InvalidFeedbackException
     */
    public function __construct($token, $data = null)
    {
        $this->token = $token;
        if ( is_string($data) )
        {
            $data = json_decode($data, true);
        }
        if ( empty($data) )
        {
            $data = array();
        }
        if ( !is_array($data) )
        {
            return;
        }

        foreach ($data as $row)
        {
            if ( is_array($row) )
            {
                $feedback = new Feedback();
                isset($row['lead_id']) && $feedback->setLeadId($row['lead_id']);
                isset($row['status']) && $feedback->setStatus($row['status']);
                isset($row['instant_at']) && $feedback->setInstantAt($row['instant_at']);
                unset($row['lead_id']);
                unset($row['status']);
                unset($row['instant_at']);
                $feedback->addExtraFields($row);

                $row = $feedback;
            }
            if ( $row instanceof Feedback )
            {
                array_push($this->collection, $row);
            }
            else
            {
                throw new InvalidFeedbackException('Unable to generate a Feedback from serialized data.'.var_dump($row, true));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        if ( $this->index >= count($this->collection) )
        {
            return null;
        }

        return $this->collection[$this->index];
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->index++;
        if ( $this->index >= count($this->collection) )
        {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        if ( $this->index >= count($this->collection) )
        {
            return null;
        }

        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Serialize the element to JSON
     *
     * @return string
     */
    public function jsonSerialize()
    {
        $serialized = array();
        foreach ($this->collection as $feedback)
        {
            array_push($serialized, $feedback->jsonSerialize());
        }
        $serialized = implode(',', $serialized);
        return "[$serialized]";
    }

    /**
     * Add Feedback to collection
     *
     * @param Feedback $feedback
     *
     * @throws InvalidFeedbackException
     */
    public function add(Feedback $feedback)
    {
        $field = $feedback->getExternalId();
        if ( ! empty($field) )
        {
            throw new InvalidFeedbackException('Using external IDs for feedback is not supported on FeedbackCollection.');
        }
        $field = $feedback->getLeadId();
        if ( empty($field) )
        {
            throw new InvalidFeedbackException('LeadId must be defined.');
        }
        $field = $feedback->getStatus();
        if ( empty($field) )
        {
            throw new InvalidFeedbackException('Status must be defined.');
        }
        $field = $feedback->getInstantAt();
        if (
            empty($field)
            || preg_match('/\d{4}-(?:0\d|1[0-2])-(?:[0-2]\d|3[0-1]) (?:[0-1]\d|2[0-3]):[0-5]\d:[0-5]\d/', $field) !== 1
        )
        {
            throw new InvalidFeedbackException('InstantAt must be defined, and must respect the format: "Y-m-d H:i:s".');
        }
        array_push($this->collection, $feedback);
    }

    /**
     * Create and add feedback to collection
     *
     * @param $leadId
     * @param $status
     * @param $instantAt
     * @param array $extraFields
     *
     * @return Feedback
     */
    public function createAndAddFeedback($leadId, $status, $instantAt, $extraFields = [])
    {
        $feedback = new Feedback();
        $feedback->setLeadId($leadId);
        $feedback->setStatus($status);
        $feedback->setInstantAt($instantAt);
        $feedback->addExtraFields($extraFields);
        $this->add($feedback);

        return $feedback;
    }

    /**
     * Send feedback to server
     *
     * @param string $baseUrl
     * @return array
     *
     * @throws InvalidFeedbackException
     * @throws InvalidTokenException
     * @throws ServiceUnavailableException
     */
    public function send($baseUrl = self::API_BASE_URL)
    {
        $utl  = "{$baseUrl}vi/{$this->token}/feedback/multiple";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $utl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
            ),
            CURLOPT_POSTFIELDS => $this->jsonSerialize()
        ));

        $result = curl_exec($curl);
        $info   = curl_getinfo($curl);
        $err    = curl_error($curl);
        $result = json_decode($result, true);

        curl_close($curl);

        if ($err)
        {
            throw new ServiceUnavailableException($err);
        }
        else if($info['http_code'] === 500)
        {
            throw new ServiceUnavailableException($result['message']);
        }
        else if ($info['http_code'] === 401)
        {
            throw new InvalidTokenException($result['message']);
        }
        else if ($info['http_code'] !== 200)
        {
            throw new InvalidFeedbackException($result['message']);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

}