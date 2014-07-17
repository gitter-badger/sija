<?php
/**
 * Response class factory.
 *
 * @package sija-framework
 * @author Alex Chermenin <alex@chermenin.ru>
 */

namespace Sija;

class Response
{
    /**
     * Constructor.
     *
     * @param int $status
     * @param mixed|object|string $data
     * @param string $format
     * @return ResponseJson
     */
    public static function create($status, $data, $format)
    {
        switch ($format) {
            case 'application/json':
            default:
                $obj = new ResponseJson($status, $data);
            break;
        }
        return $obj;
    }
}