<?php
/**
 * GetCharactersCharacterIdOk
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
 * GetCharactersCharacterIdOk Class Doc Comment
 *
 * @category    Class */
 // @description 200 ok object
/** 
 * @package     ESI
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class GetCharactersCharacterIdOk implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'get_characters_character_id_ok';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = array(
        'alliance_id' => 'int',
        'ancestry_id' => 'int',
        'birthday' => '\DateTime',
        'bloodline_id' => 'int',
        'corporation_id' => 'int',
        'description' => 'string',
        'gender' => 'string',
        'name' => 'string',
        'race_id' => 'int',
        'security_status' => 'float'
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
        'alliance_id' => 'alliance_id',
        'ancestry_id' => 'ancestry_id',
        'birthday' => 'birthday',
        'bloodline_id' => 'bloodline_id',
        'corporation_id' => 'corporation_id',
        'description' => 'description',
        'gender' => 'gender',
        'name' => 'name',
        'race_id' => 'race_id',
        'security_status' => 'security_status'
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
        'alliance_id' => 'setAllianceId',
        'ancestry_id' => 'setAncestryId',
        'birthday' => 'setBirthday',
        'bloodline_id' => 'setBloodlineId',
        'corporation_id' => 'setCorporationId',
        'description' => 'setDescription',
        'gender' => 'setGender',
        'name' => 'setName',
        'race_id' => 'setRaceId',
        'security_status' => 'setSecurityStatus'
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
        'alliance_id' => 'getAllianceId',
        'ancestry_id' => 'getAncestryId',
        'birthday' => 'getBirthday',
        'bloodline_id' => 'getBloodlineId',
        'corporation_id' => 'getCorporationId',
        'description' => 'getDescription',
        'gender' => 'getGender',
        'name' => 'getName',
        'race_id' => 'getRaceId',
        'security_status' => 'getSecurityStatus'
    );

    public static function getters()
    {
        return self::$getters;
    }

    const GENDER_FEMALE = 'female';
    const GENDER_MALE = 'male';
    

    
    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public function getGenderAllowableValues()
    {
        return [
            self::GENDER_FEMALE,
            self::GENDER_MALE,
        ];
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
        $this->container['alliance_id'] = isset($data['alliance_id']) ? $data['alliance_id'] : null;
        $this->container['ancestry_id'] = isset($data['ancestry_id']) ? $data['ancestry_id'] : null;
        $this->container['birthday'] = isset($data['birthday']) ? $data['birthday'] : null;
        $this->container['bloodline_id'] = isset($data['bloodline_id']) ? $data['bloodline_id'] : null;
        $this->container['corporation_id'] = isset($data['corporation_id']) ? $data['corporation_id'] : null;
        $this->container['description'] = isset($data['description']) ? $data['description'] : null;
        $this->container['gender'] = isset($data['gender']) ? $data['gender'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['race_id'] = isset($data['race_id']) ? $data['race_id'] : null;
        $this->container['security_status'] = isset($data['security_status']) ? $data['security_status'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = array();
        if ($this->container['birthday'] === null) {
            $invalid_properties[] = "'birthday' can't be null";
        }
        if ($this->container['bloodline_id'] === null) {
            $invalid_properties[] = "'bloodline_id' can't be null";
        }
        if ($this->container['corporation_id'] === null) {
            $invalid_properties[] = "'corporation_id' can't be null";
        }
        if ($this->container['gender'] === null) {
            $invalid_properties[] = "'gender' can't be null";
        }
        $allowed_values = array("female", "male");
        if (!in_array($this->container['gender'], $allowed_values)) {
            $invalid_properties[] = "invalid value for 'gender', must be one of #{allowed_values}.";
        }

        if ($this->container['name'] === null) {
            $invalid_properties[] = "'name' can't be null";
        }
        if ($this->container['race_id'] === null) {
            $invalid_properties[] = "'race_id' can't be null";
        }
        if (!is_null($this->container['security_status']) && ($this->container['security_status'] > 10.0)) {
            $invalid_properties[] = "invalid value for 'security_status', must be smaller than or equal to 10.0.";
        }

        if (!is_null($this->container['security_status']) && ($this->container['security_status'] < -10.0)) {
            $invalid_properties[] = "invalid value for 'security_status', must be bigger than or equal to -10.0.";
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
        if ($this->container['birthday'] === null) {
            return false;
        }
        if ($this->container['bloodline_id'] === null) {
            return false;
        }
        if ($this->container['corporation_id'] === null) {
            return false;
        }
        if ($this->container['gender'] === null) {
            return false;
        }
        $allowed_values = array("female", "male");
        if (!in_array($this->container['gender'], $allowed_values)) {
            return false;
        }
        if ($this->container['name'] === null) {
            return false;
        }
        if ($this->container['race_id'] === null) {
            return false;
        }
        if ($this->container['security_status'] > 10.0) {
            return false;
        }
        if ($this->container['security_status'] < -10.0) {
            return false;
        }
        return true;
    }


    /**
     * Gets alliance_id
     * @return int
     */
    public function getAllianceId()
    {
        return $this->container['alliance_id'];
    }

    /**
     * Sets alliance_id
     * @param int $alliance_id The character's alliance ID
     * @return $this
     */
    public function setAllianceId($alliance_id)
    {
        $this->container['alliance_id'] = $alliance_id;

        return $this;
    }

    /**
     * Gets ancestry_id
     * @return int
     */
    public function getAncestryId()
    {
        return $this->container['ancestry_id'];
    }

    /**
     * Sets ancestry_id
     * @param int $ancestry_id ancestry_id integer
     * @return $this
     */
    public function setAncestryId($ancestry_id)
    {
        $this->container['ancestry_id'] = $ancestry_id;

        return $this;
    }

    /**
     * Gets birthday
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->container['birthday'];
    }

    /**
     * Sets birthday
     * @param \DateTime $birthday Creation date of the character
     * @return $this
     */
    public function setBirthday($birthday)
    {
        $this->container['birthday'] = $birthday;

        return $this;
    }

    /**
     * Gets bloodline_id
     * @return int
     */
    public function getBloodlineId()
    {
        return $this->container['bloodline_id'];
    }

    /**
     * Sets bloodline_id
     * @param int $bloodline_id bloodline_id integer
     * @return $this
     */
    public function setBloodlineId($bloodline_id)
    {
        $this->container['bloodline_id'] = $bloodline_id;

        return $this;
    }

    /**
     * Gets corporation_id
     * @return int
     */
    public function getCorporationId()
    {
        return $this->container['corporation_id'];
    }

    /**
     * Sets corporation_id
     * @param int $corporation_id The character's corporation ID
     * @return $this
     */
    public function setCorporationId($corporation_id)
    {
        $this->container['corporation_id'] = $corporation_id;

        return $this;
    }

    /**
     * Gets description
     * @return string
     */
    public function getDescription()
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     * @param string $description description string
     * @return $this
     */
    public function setDescription($description)
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets gender
     * @return string
     */
    public function getGender()
    {
        return $this->container['gender'];
    }

    /**
     * Sets gender
     * @param string $gender gender string
     * @return $this
     */
    public function setGender($gender)
    {
        $allowed_values = array('female', 'male');
        if (!in_array($gender, $allowed_values)) {
            throw new \InvalidArgumentException("Invalid value for 'gender', must be one of 'female', 'male'");
        }
        $this->container['gender'] = $gender;

        return $this;
    }

    /**
     * Gets name
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     * @param string $name name string
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets race_id
     * @return int
     */
    public function getRaceId()
    {
        return $this->container['race_id'];
    }

    /**
     * Sets race_id
     * @param int $race_id race_id integer
     * @return $this
     */
    public function setRaceId($race_id)
    {
        $this->container['race_id'] = $race_id;

        return $this;
    }

    /**
     * Gets security_status
     * @return float
     */
    public function getSecurityStatus()
    {
        return $this->container['security_status'];
    }

    /**
     * Sets security_status
     * @param float $security_status security_status number
     * @return $this
     */
    public function setSecurityStatus($security_status)
    {

        if ($security_status > 10.0) {
            throw new \InvalidArgumentException('invalid value for $security_status when calling GetCharactersCharacterIdOk., must be smaller than or equal to 10.0.');
        }
        if ($security_status < -10.0) {
            throw new \InvalidArgumentException('invalid value for $security_status when calling GetCharactersCharacterIdOk., must be bigger than or equal to -10.0.');
        }
        $this->container['security_status'] = $security_status;

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


