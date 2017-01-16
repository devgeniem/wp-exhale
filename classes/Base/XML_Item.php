<?php
declare(strict_types=1);

namespace Exhale\Base;

class XML_Item {
    private $data;

    function __construct(array $starter_data = array()) {
        $this->data = $starter_data;
    }

    /**
     * Add item into array
     * Item can be either array('key' => 'value')
     * Or in different parameters add($item,$value)
     */
    function add($item, $value=null) {
        if (!is_array($item)) {
            $this->data[] = [$item => $value];
        } else {
            $this->data[] = $item;
        }
    }

    /**
     * Adds new non empty item into data array
     */
    function add_if_not_empty(string $key, $value, $default=null) {
        if ( ! empty($value) ) {
            if( is_array($value) ) {
                // Postmeta comes in array with only one element
                $this->data[] = array($key => $value[0]);
            } else {
                $this->data[] = array($key => $value);
            }
        } elseif(null != $default) {
            $this->data[$key] = $default;
        }
    }

    /**
     * Helper for turning arrays to strings
     */
    function add_and_implode_if_not_empty(string $key, array $values, $default=null){

        // Check that array doesn't contain null values
        if (! in_array(null, $values) ) {
            $this->data[$key] = implode($values);
        } elseif(null != $default) {
            $this->data[$key] = $default;
        }
    }

    function export(){
        return $this->data;
    }

    function get(){
        return $this->data;
    }
}
