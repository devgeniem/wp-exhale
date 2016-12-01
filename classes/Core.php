<?php
namespace Exhale;

// Use sabre xml
use Sabre\Xml\Service as XML_Service;

// Use This for excluding abstract classes from all implementing interfaces
use ReflectionClass;

/**
 * Creates xml endpoint and necessary hooks for them
 */
class Core {

    /**
     * Run the needed hooks & activations for wordpress
     */
    static function init() {
        // Respond to api endpoints.
        add_action( 'init', array( __CLASS__, 'create_routes' ) );
    }

    /**
     * Creates custom uris for exporter
     */
    static function create_routes() {

        $base_path = self::get_api_base_path();

        // Check if current route starts with base url
        if ( self::string_starts_with( $_SERVER['REQUEST_URI'], $base_path ) ) {

            $api_endpoint = substr( $_SERVER['REQUEST_URI'], strlen($base_path) );

            // Hook into xml functionality of xml was requested
            if ( self::string_ends_with($api_endpoint, '.xml') ) {

                self::handle_xml_request($api_endpoint);

            }
        }
    }

    /**
     * Returns base path where to serve exhale requests, eg. '/api/export/'
     *
     * @return string - Base path url
     */
    static function get_api_base_path() {
        // If this is subdirectory installation get the base url
        $base_path = parse_url(get_site_url())['path'];
        $base_path = ( empty($path) ? '/' : $path );

        // Add prefix for the route
        if ( defined('EXHALE_URL_PREFIX') ) {
            // Remove whitespace and '/' from beginning if any exists
            $base_path .= ltrim(EXHALE_URL_PREFIX, ' /');
        } else {
            $base_path .= 'api/export/';
        }

        return $base_path;
    }

    /*
     * Handles the xml request
     */
    static function handle_xml_request(string $endpoint) {
        if (! headers_sent()) {
            header("Content-type: text/xml");
        }

        // Remove '.xml' from the end of the $endpoint
        $vendor = preg_replace('/\.xml$/s', '', $endpoint);

        // Get classes which implement XML interface and have correct
        $handlers = self::getXMLHandlers($vendor);

        switch ( count($handlers) ) {
            case 0:
                echo self::xml_error("Endpoint is not defined");
                break;

            case 1:
                // Take the last element in the array because it's in random index after array_filter
                $handler = reset($handlers);

                echo self::create_xml_response( $handler::get_export_data(), $handler::xml_root_element(), $handler::xml_namespaces() );
                break;

            default:
                echo self::xml_error("Too many classes have defined handler for endpoint: {$endpoint}");
                break;
        }

        // End immediately after the request
        die();
    }

    /**
     * Creates xml response
     *
     * @param $data - array of elements in xml response
     * @param $root_element - XML Container for $data
     * @param $namespaces - XML namespaces for response
     */
    static function create_xml_response(array $data, array $root_element = [], array $namespaces = []) {
        // Use Sabre xml for outputting the data
        $xml_service = new XML_Service();

        // Create Sabre xml writer
        $xml_writer = $xml_service->getWriter();
        $xml_writer->openMemory();
        $xml_writer->setIndent(true);

        // Add all namespaces to xml response
        $xml_writer->namespaceMap = $namespaces;

        // Start document with explicit xml version and encoding just to be sure
        $xml_writer->startDocument('1.0', 'UTF-8');

        // Add root element and attributes for root element
        if ( isset($root_element['name']) && ! empty($root_element['name']) ) {

            $xml_writer->startElement($root_element['name']);

            if ( isset($root_element['attributes']) && ! empty($root_element['attributes']) ) {

                foreach ($root_element['attributes'] as $key => $value) {
                    $xml_writer->writeAttribute($key,$value);
                }

            }
        }

        // Write data from associative array to xml
        $xml_writer->write($data);

        // Close the root element
        if ( isset($root_element['name']) && ! empty($root_element['name']) ) {
            $xml_writer->endElement();
        }

        return $xml_writer->outputMemory();
    }

    /**
     * Xml error for debugging
     *
     * @param string $error - Error message for user
     */
    static function xml_error($error) {
        return "<error>{$error}</error>";
    }

    /**
     * String helper
     *
     * @param string $string
     * @param string $test
     */
    static function string_ends_with($string, $test) {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) {
            return false;
        }
        return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
    }

    /**
     * String helper
     *
     * @param string $string
     * @param string $test
     */
    static function string_starts_with($string, $test) {
        return ( 0 === strpos( $string, $test ) );
    }

    /**
     * Returns all classes which implement xml endpoint
     */
    static function getXMLHandlers($vendor) {
        // Get all classes which implement Exhale Exporter
        $implementors = self::getImplementingClasses(__NAMESPACE__."\Type\XML");

        // Get all vendors which serve this endpoint
        $handlers = array_filter( $implementors, function( $implementor ) use ( $vendor ) {
                // Check if the name of the class which implements Vendor\XML
                // matches to requested endpoint
                $split = explode('\\',$implementor);
                return ( strtolower( end($split) )  === $vendor );
            }
        );

        return $handlers;
    }

    /**
     * Returns all classes which implement certain interface and
     * which are not abstract classes
     *
     * @param $interfaceName
     */
    static function getImplementingClasses( $interfaceName ) {
        return array_filter(
            get_declared_classes(),
            function( $className ) use ( $interfaceName ) {
                //
                return ( ! (new ReflectionClass('\\'.$className))->isAbstract() &&
                        in_array( $interfaceName, class_implements( $className ) ) );
            }
        );
    }
}
