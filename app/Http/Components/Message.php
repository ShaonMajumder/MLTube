<?php

namespace App\Http\Components;

trait Message{

    /**
     * Set Valiables with Default Value
     */
    protected $status   = false;
    protected $message  = "Error";
    protected $access_token;
    protected $data;
    
    /**
     * Set API Success Values
     */
    protected function apiSuccess($message = null){
        $this->status = true;
        $this->message = empty($message) ? "Successfully" : $message;
    }

    /**
     * @return API Response
     */
    protected function apiOutput($status_code = 200, $message = null){
        if(!empty($this->access_token)){
            $this->data = array_merge(
                [
                    "access_token" => $this->access_token,
                    "token_type"  => "Bearer"
                ],
                $this->data
            );
        }
        
        $return_arr = [
            "status"        => $this->status,
            "message"       => !empty($message) ? $message : $this->message,
            "data"          => $this->data,
        ];

        if( empty($this->access_token) ){
            unset($return_arr["access_token"]);
            unset($return_arr["token_type"]);
        }
        
        return response($return_arr,  $status_code);
    }

    /**
     * Retrive Validation Error
     */
    protected function getValidationError($validate = null){
        if( empty($validate) ){
            return "Data Validation Error";
        }
        return $validate->errors()->first();
    }

    /**
     * Get Exceptional Error
     */
    protected function getExceptionError($e){
        if( env("APP_ENV") == "local"){
            return $e->getMessage() . ' On File' .$e->getFile() . ':' .$e->getLine();
        }
        return "Server Error";
    }
}