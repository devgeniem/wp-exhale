<?php
declare(strict_types=1);

namespace Exhale\Vendor;

/**
 * This class exists so that users of Exhale can start producing xml really quickly
 */
abstract class Oikotie implements \Exhale\Type\XML {

    /**
     * Returns exportable apartments to Oikotie
     */
    abstract static public function get_export_data() : array;

    /**
     * Define root element and extra attributes
     */
    static public function xml_root_element() : array {
        return [
            'name' => 'Apartments',
            'attributes' => [
                'xsi:noNamespaceSchemaLocation' => '/docs/ecg/apartments/OT-apartments.xsd'
            ]
        ];
    }

    /**
     * Define namespaces into root element
     */
    static public function xml_namespaces() : array {
        return [
            'http://www.w3.org/2001/XMLSchema-instance' => 'xsi'
        ];
    }
}

