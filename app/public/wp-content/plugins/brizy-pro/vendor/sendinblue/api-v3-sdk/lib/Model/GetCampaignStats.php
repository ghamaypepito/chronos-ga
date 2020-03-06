<?php
/**
 * GetCampaignStats
 *
 * PHP version 5
 *
 * @category Class
 * @package  SendinBlue\Client
 * @author   Swaagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * SendinBlue API
 *
 * SendinBlue provide a RESTFul API that can be used with any languages. With this API, you will be able to :   - Manage your campaigns and get the statistics   - Manage your contacts   - Send transactional Emails and SMS   - and much more...  You can download our wrappers at https://github.com/orgs/sendinblue  **Possible responses**   | Code | Message |   | :-------------: | ------------- |   | 200  | OK. Successful Request  |   | 201  | OK. Successful Creation |   | 202  | OK. Request accepted |   | 204  | OK. Successful Update/Deletion  |   | 400  | Error. Bad Request  |   | 401  | Error. Authentication Needed  |   | 402  | Error. Not enough credit, plan upgrade needed  |   | 403  | Error. Permission denied  |   | 404  | Error. Object does not exist |   | 405  | Error. Method not allowed  |
 *
 * OpenAPI spec version: 3.0.0
 * Contact: contact@sendinblue.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace SendinBlue\Client\Model;

use \ArrayAccess;

/**
 * GetCampaignStats Class Doc Comment
 *
 * @category    Class
 * @package     SendinBlue\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class GetCampaignStats implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'getCampaignStats';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'listId' => 'int',
        'uniqueClicks' => 'int',
        'clickers' => 'int',
        'complaints' => 'int',
        'delivered' => 'int',
        'sent' => 'int',
        'softBounces' => 'int',
        'hardBounces' => 'int',
        'uniqueViews' => 'int',
        'unsubscriptions' => 'int',
        'viewed' => 'int',
        'deferred' => 'int'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerFormats = [
        'listId' => 'int64',
        'uniqueClicks' => 'int64',
        'clickers' => 'int64',
        'complaints' => 'int64',
        'delivered' => 'int64',
        'sent' => 'int64',
        'softBounces' => 'int64',
        'hardBounces' => 'int64',
        'uniqueViews' => 'int64',
        'unsubscriptions' => 'int64',
        'viewed' => 'int64',
        'deferred' => 'int64'
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'listId' => 'listId',
        'uniqueClicks' => 'uniqueClicks',
        'clickers' => 'clickers',
        'complaints' => 'complaints',
        'delivered' => 'delivered',
        'sent' => 'sent',
        'softBounces' => 'softBounces',
        'hardBounces' => 'hardBounces',
        'uniqueViews' => 'uniqueViews',
        'unsubscriptions' => 'unsubscriptions',
        'viewed' => 'viewed',
        'deferred' => 'deferred'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'listId' => 'setListId',
        'uniqueClicks' => 'setUniqueClicks',
        'clickers' => 'setClickers',
        'complaints' => 'setComplaints',
        'delivered' => 'setDelivered',
        'sent' => 'setSent',
        'softBounces' => 'setSoftBounces',
        'hardBounces' => 'setHardBounces',
        'uniqueViews' => 'setUniqueViews',
        'unsubscriptions' => 'setUnsubscriptions',
        'viewed' => 'setViewed',
        'deferred' => 'setDeferred'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'listId' => 'getListId',
        'uniqueClicks' => 'getUniqueClicks',
        'clickers' => 'getClickers',
        'complaints' => 'getComplaints',
        'delivered' => 'getDelivered',
        'sent' => 'getSent',
        'softBounces' => 'getSoftBounces',
        'hardBounces' => 'getHardBounces',
        'uniqueViews' => 'getUniqueViews',
        'unsubscriptions' => 'getUnsubscriptions',
        'viewed' => 'getViewed',
        'deferred' => 'getDeferred'
    ];

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    public static function setters()
    {
        return self::$setters;
    }

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     * @param mixed[] $data Associated array of property values initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['listId'] = isset($data['listId']) ? $data['listId'] : null;
        $this->container['uniqueClicks'] = isset($data['uniqueClicks']) ? $data['uniqueClicks'] : null;
        $this->container['clickers'] = isset($data['clickers']) ? $data['clickers'] : null;
        $this->container['complaints'] = isset($data['complaints']) ? $data['complaints'] : null;
        $this->container['delivered'] = isset($data['delivered']) ? $data['delivered'] : null;
        $this->container['sent'] = isset($data['sent']) ? $data['sent'] : null;
        $this->container['softBounces'] = isset($data['softBounces']) ? $data['softBounces'] : null;
        $this->container['hardBounces'] = isset($data['hardBounces']) ? $data['hardBounces'] : null;
        $this->container['uniqueViews'] = isset($data['uniqueViews']) ? $data['uniqueViews'] : null;
        $this->container['unsubscriptions'] = isset($data['unsubscriptions']) ? $data['unsubscriptions'] : null;
        $this->container['viewed'] = isset($data['viewed']) ? $data['viewed'] : null;
        $this->container['deferred'] = isset($data['deferred']) ? $data['deferred'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];

        if ($this->container['uniqueClicks'] === null) {
            $invalid_properties[] = "'uniqueClicks' can't be null";
        }
        if ($this->container['clickers'] === null) {
            $invalid_properties[] = "'clickers' can't be null";
        }
        if ($this->container['complaints'] === null) {
            $invalid_properties[] = "'complaints' can't be null";
        }
        if ($this->container['delivered'] === null) {
            $invalid_properties[] = "'delivered' can't be null";
        }
        if ($this->container['sent'] === null) {
            $invalid_properties[] = "'sent' can't be null";
        }
        if ($this->container['softBounces'] === null) {
            $invalid_properties[] = "'softBounces' can't be null";
        }
        if ($this->container['hardBounces'] === null) {
            $invalid_properties[] = "'hardBounces' can't be null";
        }
        if ($this->container['uniqueViews'] === null) {
            $invalid_properties[] = "'uniqueViews' can't be null";
        }
        if ($this->container['unsubscriptions'] === null) {
            $invalid_properties[] = "'unsubscriptions' can't be null";
        }
        if ($this->container['viewed'] === null) {
            $invalid_properties[] = "'viewed' can't be null";
        }
        if ($this->container['deferred'] === null) {
            $invalid_properties[] = "'deferred' can't be null";
        }
        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {

        if ($this->container['uniqueClicks'] === null) {
            return false;
        }
        if ($this->container['clickers'] === null) {
            return false;
        }
        if ($this->container['complaints'] === null) {
            return false;
        }
        if ($this->container['delivered'] === null) {
            return false;
        }
        if ($this->container['sent'] === null) {
            return false;
        }
        if ($this->container['softBounces'] === null) {
            return false;
        }
        if ($this->container['hardBounces'] === null) {
            return false;
        }
        if ($this->container['uniqueViews'] === null) {
            return false;
        }
        if ($this->container['unsubscriptions'] === null) {
            return false;
        }
        if ($this->container['viewed'] === null) {
            return false;
        }
        if ($this->container['deferred'] === null) {
            return false;
        }
        return true;
    }


    /**
     * Gets listId
     * @return int
     */
    public function getListId()
    {
        return $this->container['listId'];
    }

    /**
     * Sets listId
     * @param int $listId List Id of email campaign (only in case of get email campaign(s))
     * @return $this
     */
    public function setListId($listId)
    {
        $this->container['listId'] = $listId;

        return $this;
    }

    /**
     * Gets uniqueClicks
     * @return int
     */
    public function getUniqueClicks()
    {
        return $this->container['uniqueClicks'];
    }

    /**
     * Sets uniqueClicks
     * @param int $uniqueClicks Number of unique clicks for the campaign
     * @return $this
     */
    public function setUniqueClicks($uniqueClicks)
    {
        $this->container['uniqueClicks'] = $uniqueClicks;

        return $this;
    }

    /**
     * Gets clickers
     * @return int
     */
    public function getClickers()
    {
        return $this->container['clickers'];
    }

    /**
     * Sets clickers
     * @param int $clickers Number of clicks for the campaign
     * @return $this
     */
    public function setClickers($clickers)
    {
        $this->container['clickers'] = $clickers;

        return $this;
    }

    /**
     * Gets complaints
     * @return int
     */
    public function getComplaints()
    {
        return $this->container['complaints'];
    }

    /**
     * Sets complaints
     * @param int $complaints Number of complaints (Spam reports) for the campaign
     * @return $this
     */
    public function setComplaints($complaints)
    {
        $this->container['complaints'] = $complaints;

        return $this;
    }

    /**
     * Gets delivered
     * @return int
     */
    public function getDelivered()
    {
        return $this->container['delivered'];
    }

    /**
     * Sets delivered
     * @param int $delivered Number of delivered emails for the campaign
     * @return $this
     */
    public function setDelivered($delivered)
    {
        $this->container['delivered'] = $delivered;

        return $this;
    }

    /**
     * Gets sent
     * @return int
     */
    public function getSent()
    {
        return $this->container['sent'];
    }

    /**
     * Sets sent
     * @param int $sent Number of sent emails for the campaign
     * @return $this
     */
    public function setSent($sent)
    {
        $this->container['sent'] = $sent;

        return $this;
    }

    /**
     * Gets softBounces
     * @return int
     */
    public function getSoftBounces()
    {
        return $this->container['softBounces'];
    }

    /**
     * Sets softBounces
     * @param int $softBounces Number of softbounce for the campaign
     * @return $this
     */
    public function setSoftBounces($softBounces)
    {
        $this->container['softBounces'] = $softBounces;

        return $this;
    }

    /**
     * Gets hardBounces
     * @return int
     */
    public function getHardBounces()
    {
        return $this->container['hardBounces'];
    }

    /**
     * Sets hardBounces
     * @param int $hardBounces Number of harbounce for the campaign
     * @return $this
     */
    public function setHardBounces($hardBounces)
    {
        $this->container['hardBounces'] = $hardBounces;

        return $this;
    }

    /**
     * Gets uniqueViews
     * @return int
     */
    public function getUniqueViews()
    {
        return $this->container['uniqueViews'];
    }

    /**
     * Sets uniqueViews
     * @param int $uniqueViews Number of unique openings for the campaign
     * @return $this
     */
    public function setUniqueViews($uniqueViews)
    {
        $this->container['uniqueViews'] = $uniqueViews;

        return $this;
    }

    /**
     * Gets unsubscriptions
     * @return int
     */
    public function getUnsubscriptions()
    {
        return $this->container['unsubscriptions'];
    }

    /**
     * Sets unsubscriptions
     * @param int $unsubscriptions Number of unsubscription for the campaign
     * @return $this
     */
    public function setUnsubscriptions($unsubscriptions)
    {
        $this->container['unsubscriptions'] = $unsubscriptions;

        return $this;
    }

    /**
     * Gets viewed
     * @return int
     */
    public function getViewed()
    {
        return $this->container['viewed'];
    }

    /**
     * Sets viewed
     * @param int $viewed Number of openings for the campaign
     * @return $this
     */
    public function setViewed($viewed)
    {
        $this->container['viewed'] = $viewed;

        return $this;
    }

    /**
     * Gets deferred
     * @return int
     */
    public function getDeferred()
    {
        return $this->container['deferred'];
    }

    /**
     * Sets deferred
     * @param int $deferred Number of deferred emails for the campaign
     * @return $this
     */
    public function setDeferred($deferred)
    {
        $this->container['deferred'] = $deferred;

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
            return json_encode(\SendinBlue\Client\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\SendinBlue\Client\ObjectSerializer::sanitizeForSerialization($this));
    }
}


