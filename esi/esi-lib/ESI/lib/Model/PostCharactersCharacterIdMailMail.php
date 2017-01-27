<?php
/**
 * PostCharactersCharacterIdMailMail
 *
 * PHP version 5
 *
 * @category Class
 * @package  ESI
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * EVE Swagger Interface
 *
 * An OpenAPI for EVE Online
 *
 * OpenAPI spec version: 0.3.9
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace ESI\Model;

use \ArrayAccess;

/**
 * PostCharactersCharacterIdMailMail Class Doc Comment
 *
 * @category    Class */
 // @description mail schema
/** 
 * @package     ESI
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class PostCharactersCharacterIdMailMail implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'post_characters_character_id_mail_mail';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = array(
        'approved_cost' => 'int',
        'body' => 'string',
        'recipients' => '\ESI\Model\CharacterscharacterIdmailRecipients1[]',
        'subject' => 'string'
    );

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = array(
        'approved_cost' => 'approved_cost',
        'body' => 'body',
        'recipients' => 'recipients',
        'subject' => 'subject'
    );

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = array(
        'approved_cost' => 'setApprovedCost',
        'body' => 'setBody',
        'recipients' => 'setRecipients',
        'subject' => 'setSubject'
    );

    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = array(
        'approved_cost' => 'getApprovedCost',
        'body' => 'getBody',
        'recipients' => 'getRecipients',
        'subject' => 'getSubject'
    );

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = array();

    /**
     * Constructor
     * @param mixed[] $data Associated array of property value initalizing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['approved_cost'] = isset($data['approved_cost']) ? $data['approved_cost'] : 0;
        $this->container['body'] = isset($data['body']) ? $data['body'] : null;
        $this->container['recipients'] = isset($data['recipients']) ? $data['recipients'] : null;
        $this->container['subject'] = isset($data['subject']) ? $data['subject'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = array();
        if ($this->container['body'] === null) {
            $invalid_properties[] = "'body' can't be null";
        }
        if ((strlen($this->container['body']) > 10000)) {
            $invalid_properties[] = "invalid value for 'body', the character length must be smaller than or equal to 10000.";
        }

        if ($this->container['recipients'] === null) {
            $invalid_properties[] = "'recipients' can't be null";
        }
        if ($this->container['subject'] === null) {
            $invalid_properties[] = "'subject' can't be null";
        }
        if ((strlen($this->container['subject']) > 1000)) {
            $invalid_properties[] = "invalid value for 'subject', the character length must be smaller than or equal to 1000.";
        }

        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properteis are valid
     */
    public function valid()
    {
        if ($this->container['body'] === null) {
            return false;
        }
        if (strlen($this->container['body']) > 10000) {
            return false;
        }
        if ($this->container['recipients'] === null) {
            return false;
        }
        if ($this->container['subject'] === null) {
            return false;
        }
        if (strlen($this->container['subject']) > 1000) {
            return false;
        }
        return true;
    }


    /**
     * Gets approved_cost
     * @return int
     */
    public function getApprovedCost()
    {
        return $this->container['approved_cost'];
    }

    /**
     * Sets approved_cost
     * @param int $approved_cost approved_cost integer
     * @return $this
     */
    public function setApprovedCost($approved_cost)
    {
        $this->container['approved_cost'] = $approved_cost;

        return $this;
    }

    /**
     * Gets body
     * @return string
     */
    public function getBody()
    {
        return $this->container['body'];
    }

    /**
     * Sets body
     * @param string $body body string
     * @return $this
     */
    public function setBody($body)
    {
        if (strlen($body) > 10000) {
            throw new \InvalidArgumentException('invalid length for $body when calling PostCharactersCharacterIdMailMail., must be smaller than or equal to 10000.');
        }
        $this->container['body'] = $body;

        return $this;
    }

    /**
     * Gets recipients
     * @return \ESI\Model\CharacterscharacterIdmailRecipients1[]
     */
    public function getRecipients()
    {
        return $this->container['recipients'];
    }

    /**
     * Sets recipients
     * @param \ESI\Model\CharacterscharacterIdmailRecipients1[] $recipients recipients array
     * @return $this
     */
    public function setRecipients($recipients)
    {
        $this->container['recipients'] = $recipients;

        return $this;
    }

    /**
     * Gets subject
     * @return string
     */
    public function getSubject()
    {
        return $this->container['subject'];
    }

    /**
     * Sets subject
     * @param string $subject subject string
     * @return $this
     */
    public function setSubject($subject)
    {
        if (strlen($subject) > 1000) {
            throw new \InvalidArgumentException('invalid length for $subject when calling PostCharactersCharacterIdMailMail., must be smaller than or equal to 1000.');
        }
        $this->container['subject'] = $subject;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\ESI\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\ESI\ObjectSerializer::sanitizeForSerialization($this));
    }
}


