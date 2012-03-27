<?php
/**
 * @author mlapko
 * @version 0.01
 */
class JB 
{
    /**
     * Store current properties
     */
    private $_jObject;
    
    /**
     * Vars
     * @var array 
     */
    private static $_vars = array();

    public function __construct($object = null) 
    {
        $this->_jObject = $object === null ? new stdClass() : $object;
    }
    
    /**
     * Encode to json
     * @param callback $fn
     * 
     * @return string JSON
     */
    public static function encode($fn) 
    {
        $json = new self();
        $fn($json);
        return "$json";
    }    
    
    /**
     *
     * @return stdClass 
     */
    public function getObject()
    {
        return $this->_jObject;
    }
    
    /**
     * Set property to current object
     * @param stdClass $data
     * @param string|array $fields 
     */
    public function set($data, $fields = array())
    {
        if (empty($fields)) {
            foreach ($this->_getEntityVars($data) as $key => $value) {
                $this->_jObject->{$key} = $value;
            }            
        } else {
            $fields = is_array($fields) ? $fields : preg_split('/\s*,\s*/', trim($fields), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($fields as $fieldName) {
                $this->_jObject->{$fieldName} = $data->{$fieldName};
            }            
        }
        return $this;
    }    
    
    /**
     * Set var for JB
     * 
     * @param string|array $name
     * @param mixed $value 
     */
    public static function setVar($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                self::$_vars[$k] = $v;
            }
        } else {
            self::$_vars[$name] = $value;            
        }
    }
    
    /**
     * Get setted var
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    public static function getVar($name, $default = null)
    {
        return isset(self::$_vars[$name]) ? self::$_vars[$name] : $default;
    }    

    public function __set($name, $value) 
    {
        return $this->_jObject->{$name} = $value;
    }

    public function __call($name, $args) 
    {
        if (count($args) === 1 && is_callable($args[0])) {
            $json = new self();
            call_user_func($args[0], $json);
            $this->_jObject->$name = $json->getObject();
        } elseif (count($args) == 2 && is_callable($args[1])) {
            $result = array();
            foreach ($args[0] as $e) {
                $json = new self();
                $temp = call_user_func($args[1], $json, $e);
                $result[] = isset($temp) ? $temp : $json->getObject();
            }
            $this->_jObject->{$name} = $result;
        } else {
            $this->_jObject->{$name} = $args[0];
        }
    }

    /**
     * Convert object to string
     * @return string 
     */
    public function __toString() 
    {
        return CJSON::encode($this->_jObject);
    }    
    
    /**
     * Get property
     * @param mixed $entity
     * @return array
     */
    protected function _getEntityVars($entity)
    {
        $type = gettype($entity);
        if ($entity === 'array') {
            return $entity;
        } elseif ($entity === 'object') {
            if ($entity instanceof Traversable) {
                return $entity;
            }
            return get_object_vars($entity);
        }
        return array(); 
    }
}