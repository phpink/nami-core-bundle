<?php

namespace PhpInk\Nami\CoreBundle\Decoder;

use FOS\RestBundle\Decoder\DecoderInterface;

/**
 * Remove extra fields from JSON data
 * for FormTypes validation (fields like _references, links, ..)
 */
class JsonDecoder implements DecoderInterface
{
    /**
     * Request param keys to remove
     *
     * @var array
     */
    private $extraFields = array(
        '_links' => true,
        '_references' => true
    );

    /**
     * @var DocumentManager|EntityManager
     */
    protected $em;

    /**
     * Remove extra fields
     * from the JSON input data
     *
     * @param array $data
     * @return array $data
     */
    private function transformInputData($data)
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->extraFields)) {
                // REMOVE extra field
                unset($data[$key]);

            } else if (is_array($value)) {
                // Converts DATA recursively
                $data[$key] = $this->transformInputData($value);

            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function decode($data)
    {
        $decodedData = json_decode($data, true);
        if ($decodedData) {
            $decodedData = $this->xWwwFormEncodedLike($decodedData);
            $decodedData = $this->transformInputData($decodedData);
        }
        return $decodedData;
    }

    /**
     * Makes data decoded from
     * JSON application/x-www-form-encoded compliant
     *
     * CODE FROM: FOS\RestBundle\Decoder\JsonToFormDecoder
     *
     * @param array $data
     * @return array
     */
    private function xWwwFormEncodedLike($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Encode recursively
                $data[$key] = $this->xWwwFormEncodedLike($value);
            } elseif (false === $value) {
                // Checkbox-like behavior removes false data but PATCH HTTP method with just checkboxes does not work
                // To fix this issue we prefer transform false data to null
                // See https://github.com/FriendsOfSymfony/FOSRestBundle/pull/883
                $data[$key] = null;
            } elseif (!is_string($value)) {
                // Convert everything to string
                // true values will be converted to '1', this is the default checkbox behavior
                $data[$key] = strval($value);
            }
        }
        return $data;
    }

    /**
     * @var DocumentManager|EntityManager $em
     */
    public function setManager($em)
    {
        $this->em = $em;
    }

}
