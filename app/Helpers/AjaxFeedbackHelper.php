<?php

namespace App\Helpers;

class AjaxFeedbackHelper
{
    protected $status = 'FAILURE';
    protected $code = 0;
    protected $message = '';
    protected $data = null;
    protected $errors = [];

    protected $_statuses = ['SUCCESS', 'FAILURE', 'DEBUG', 'REDIRECT'];

    public function __construct($status = 'FAILURE', $code = 0, $message = 'Nothing Happened', $data = null, $errors = [])
    {
        $this->set('status', $status);
        $this->set('code', $code);
        $this->set('message', $message);
        $this->set('data', $data);
        $this->set('errors', $errors);
    }


    protected function set($key, $value)
    {
        switch ($key) {
            case 'status':
                if (in_array(strtoupper($value), $this->_statuses)) {
                    $this->status = strtoupper($value);
                } elseif (true === $value) {
                    $this->status = 'SUCCESS';
                } elseif (false === $value) {
                    $this->status = 'FAILURE';
                }
                break;

            case 'code':
                $this->code = intval($value);
                if ($this->code < 0) {
                    $this->code = $this->code * -1;
                }
                break;

            case 'message':
                $this->message = strval($value);
                break;

            case 'data':
                $this->data = $value;
                break;

            case 'errors':
                if (is_array($value)) {
                    $this->errors = array_unique(array_merge($this->errors, $value));
                }
                break;
                
            default:
                return false;
                break;
        }
    }

    public function asResponse($pretty = false)
    {
        $ret = new \stdClass();
        $ret->status = $this->status;
        $ret->code = $this->code;
        $ret->message = $this->message;
        $ret->data = $this->data;
        $ret->errors = $this->errors;
        $fb = ( true == $pretty ) ? json_encode($ret, JSON_PRETTY_PRINT) : json_encode($ret);
        return response($fb, intval($this->code))
                ->header('Content-Type', 'application/json');
    }

    public static function success($data = null, $message = 'Action Completed Successfully', $code = 200)
    {
        $c = get_called_class();
        $obj = new $c(true, $code, $message, $data, []);
        return $obj;
    }

    public static function failure($data = null, $message = 'Action Failed', $code = 500, $errors = [])
    {
        $c = get_called_class();
        $obj = new $c(false, $code, $message, $data, $errors);
        return $obj;
    }

    public static function debug($data = null, $message = 'Debugging', $code = 200, $errors = [])
    {
        $c = get_called_class();
        $obj = new $c('DEBUG', $code, $message, $data, $errors);
        return $obj;
    }

    public static function successResponse($data = null, $message = 'Action Completed Successfully', $code = 200, $pretty = false)
    {
        $obj = self::success($data, $message, $code);
        return $obj->asResponse($pretty);
    }

    public static function failureReponse($data = null, $message = 'Action Failed', $code = 500, $errors = [], $pretty = false)
    {
        $obj = self::failure($data, $message, $code, $errors);
        return $obj->asResponse($pretty);
    }

    public static function debugReponse($data = null, $message = 'Debugging', $code = 200, $errors = [], $pretty = false)
    {
        $obj = self::debug($data, $message, $code, $errors);
        return $obj->asResponse($pretty);
    }
}
