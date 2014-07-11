<?php
/**
 * Response class factory.
 * 
 * @package sija-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 */
class Response
{
    /**
     * Constructor.
     *
     * @param int $status
     * @param string $data
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