<?php
/**
 * Response class factory.
 *
 * @package Sija\Common
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija\Common;

class ResponseBuilder {

    /**
     * Response constructor.
     *
     * @param int $status
     * @param mixed $data
     * @param string $format
     * @return Response
     */
    public static function create($status, $data, $format)
    {
        switch ($format) {

            case 'application/xml':
            case 'text/xml':
                $obj = new ResponseXml($status, $data);
                break;

            case 'application/json':
            default:
                $obj = new ResponseJson($status, $data);
                break;

        }
        return $obj;
    }

}