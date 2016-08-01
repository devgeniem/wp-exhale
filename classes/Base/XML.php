<?php
declare(strict_types=1);

namespace Exhale\Base;

/**
 * This class exists so that users of Exhale can start producing xml really quickly
 */
abstract class XML implements \Exhale\Type\XML {
    /*
     * Returns exportable apartments to Vuokraovi
     */
    abstract static public function get_export_data() : array;

    /*
     * Empty root element is ignored
     */
    static public function xml_root_element() : array {
        return array();
    }

    /**
     * Empty namespaces are ignored
     */
    static public function xml_namespaces() : array {
        return array();
    }
}

