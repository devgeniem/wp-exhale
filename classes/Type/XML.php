<?php
declare(strict_types=1);

namespace Exhale\Type;

/**
 * Extendable interface for creating exporters easily
 */
interface XML {
    static public function get_export_data() : array;
    static public function xml_namespaces() : array;
    static public function xml_root_element() : array;
    // TODO: add validator()
}
