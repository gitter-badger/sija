<?php
/**
 * JSON response class.
 * 
 * @package api-framework
 * @author  Alex Chermenin <alex@chermenin.ru>
 */
class ResponseJson
{
    /**
     * Response status.
     *
     * @var int
     */
    protected $status;
    
    /**
     * Response data.
     *
     * @var string
     */
    protected $data;
    
    /**
     * Constructor.
     *
     * @param $status
     * @param string $data
     */
    public function __construct($status, $data)
    {
        $this->status = $status;
        $this->data = $data;
        return $this;
    }
    
    /**
     * Render the response as JSON.
     * 
     * @return string
     */
    public function render()
    {
        header('Content-Type: application/json');
        return json_encode(array('status' => $this->status, 'response' => $this->data));
    }
}