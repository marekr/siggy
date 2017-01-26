<?php
/**
 * GetSovereigntyStructures200Ok
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
 * GetSovereigntyStructures200Ok Class Doc Comment
 *
 * @category    Class */
 // @description 200 ok object
/** 
 * @package     ESI
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class GetSovereigntyStructures200Ok implements ArrayAccess
{
    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'get_sovereignty_structures_200_ok';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = array(
        'allianceId' => 'int',
        'solarSystemId' => 'int',
        'structureId' => 'int',
        'structureTypeId' => 'int',
        'vulnerabilityOccupancyLevel' => 'float',
        'vulnerableEndTime' => '\DateTime',
        'vulnerableStartTime' => '\DateTime'
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
        'allianceId' => 'alliance_id',
        'solarSystemId' => 'solar_system_id',
        'structureId' => 'structure_id',
        'structureTypeId' => 'structure_type_id',
        'vulnerabilityOccupancyLevel' => 'vulnerability_occupancy_level',
        'vulnerableEndTime' => 'vulnerable_end_time',
        'vulnerableStartTime' => 'vulnerable_start_time'
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
        'allianceId' => 'setAllianceId',
        'solarSystemId' => 'setSolarSystemId',
        'structureId' => 'setStructureId',
        'structureTypeId' => 'setStructureTypeId',
        'vulnerabilityOccupancyLevel' => 'setVulnerabilityOccupancyLevel',
        'vulnerableEndTime' => 'setVulnerableEndTime',
        'vulnerableStartTime' => 'setVulnerableStartTime'
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
        'allianceId' => 'getAllianceId',
        'solarSystemId' => 'getSolarSystemId',
        'structureId' => 'getStructureId',
        'structureTypeId' => 'getStructureTypeId',
        'vulnerabilityOccupancyLevel' => 'getVulnerabilityOccupancyLevel',
        'vulnerableEndTime' => 'getVulnerableEndTime',
        'vulnerableStartTime' => 'getVulnerableStartTime'
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
        $this->container['allianceId'] = isset($data['allianceId']) ? $data['allianceId'] : null;
        $this->container['solarSystemId'] = isset($data['solarSystemId']) ? $data['solarSystemId'] : null;
        $this->container['structureId'] = isset($data['structureId']) ? $data['structureId'] : null;
        $this->container['structureTypeId'] = isset($data['structureTypeId']) ? $data['structureTypeId'] : null;
        $this->container['vulnerabilityOccupancyLevel'] = isset($data['vulnerabilityOccupancyLevel']) ? $data['vulnerabilityOccupancyLevel'] : null;
        $this->container['vulnerableEndTime'] = isset($data['vulnerableEndTime']) ? $data['vulnerableEndTime'] : null;
        $this->container['vulnerableStartTime'] = isset($data['vulnerableStartTime']) ? $data['vulnerableStartTime'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = array();
        if ($this->container['allianceId'] === null) {
            $invalid_properties[] = "'allianceId' can't be null";
        }
        if ($this->container['solarSystemId'] === null) {
            $invalid_properties[] = "'solarSystemId' can't be null";
        }
        if ($this->container['structureId'] === null) {
            $invalid_properties[] = "'structureId' can't be null";
        }
        if ($this->container['structureTypeId'] === null) {
            $invalid_properties[] = "'structureTypeId' can't be null";
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
        if ($this->container['allianceId'] === null) {
            return false;
        }
        if ($this->container['solarSystemId'] === null) {
            return false;
        }
        if ($this->container['structureId'] === null) {
            return false;
        }
        if ($this->container['structureTypeId'] === null) {
            return false;
        }
        return true;
    }


    /**
     * Gets allianceId
     * @return int
     */
    public function getAllianceId()
    {
        return $this->container['allianceId'];
    }

    /**
     * Sets allianceId
     * @param int $allianceId The alliance that owns the structure.
     * @return $this
     */
    public function setAllianceId($allianceId)
    {
        $this->container['allianceId'] = $allianceId;

        return $this;
    }

    /**
     * Gets solarSystemId
     * @return int
     */
    public function getSolarSystemId()
    {
        return $this->container['solarSystemId'];
    }

    /**
     * Sets solarSystemId
     * @param int $solarSystemId Solar system in which the structure is located.
     * @return $this
     */
    public function setSolarSystemId($solarSystemId)
    {
        $this->container['solarSystemId'] = $solarSystemId;

        return $this;
    }

    /**
     * Gets structureId
     * @return int
     */
    public function getStructureId()
    {
        return $this->container['structureId'];
    }

    /**
     * Sets structureId
     * @param int $structureId Unique item ID for this structure.
     * @return $this
     */
    public function setStructureId($structureId)
    {
        $this->container['structureId'] = $structureId;

        return $this;
    }

    /**
     * Gets structureTypeId
     * @return int
     */
    public function getStructureTypeId()
    {
        return $this->container['structureTypeId'];
    }

    /**
     * Sets structureTypeId
     * @param int $structureTypeId A reference to the type of structure this is.
     * @return $this
     */
    public function setStructureTypeId($structureTypeId)
    {
        $this->container['structureTypeId'] = $structureTypeId;

        return $this;
    }

    /**
     * Gets vulnerabilityOccupancyLevel
     * @return float
     */
    public function getVulnerabilityOccupancyLevel()
    {
        return $this->container['vulnerabilityOccupancyLevel'];
    }

    /**
     * Sets vulnerabilityOccupancyLevel
     * @param float $vulnerabilityOccupancyLevel The occupancy level for the next or current vulnerability window. This takes into account all development indexes and capital system bonuses. Also known as Activity Defense Multiplier from in the client. It increases the time that attackers must spend using their entosis links on the structure.
     * @return $this
     */
    public function setVulnerabilityOccupancyLevel($vulnerabilityOccupancyLevel)
    {
        $this->container['vulnerabilityOccupancyLevel'] = $vulnerabilityOccupancyLevel;

        return $this;
    }

    /**
     * Gets vulnerableEndTime
     * @return \DateTime
     */
    public function getVulnerableEndTime()
    {
        return $this->container['vulnerableEndTime'];
    }

    /**
     * Sets vulnerableEndTime
     * @param \DateTime $vulnerableEndTime The time at which the next or current vulnerability window ends. At the end of a vulnerability window the next window is recalculated and locked in along with the vulnerabilityOccupancyLevel. If the structure is not in 100% entosis control of the defender, it will go in to 'overtime' and stay vulnerable for as long as that situation persists. Only once the defenders have 100% entosis control and has the vulnerableEndTime passed does the vulnerability interval expire and a new one is calculated.
     * @return $this
     */
    public function setVulnerableEndTime($vulnerableEndTime)
    {
        $this->container['vulnerableEndTime'] = $vulnerableEndTime;

        return $this;
    }

    /**
     * Gets vulnerableStartTime
     * @return \DateTime
     */
    public function getVulnerableStartTime()
    {
        return $this->container['vulnerableStartTime'];
    }

    /**
     * Sets vulnerableStartTime
     * @param \DateTime $vulnerableStartTime The next time at which the structure will become vulnerable. Or the start time of the current window if current time is between this and vulnerableEndTime.
     * @return $this
     */
    public function setVulnerableStartTime($vulnerableStartTime)
    {
        $this->container['vulnerableStartTime'] = $vulnerableStartTime;

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


