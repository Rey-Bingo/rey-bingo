<?php

namespace App\ThirdParty\WebPush;

class NotificationResult {
    private $httpCode;
    private $response;
    private $error;
    private $endpoint;
    
    public function __construct($httpCode, $response, $error, $endpoint)
    {
        $this->httpCode = $httpCode;
        $this->response = $response;
        $this->error = $error;
        $this->endpoint = $endpoint;
    }
    
    public function isSuccess()
    {
        return $this->httpCode >= 200 && $this->httpCode < 300;
    }
    
    public function isSubscriptionExpired()
    {
        return in_array($this->httpCode, [404, 410]);
    }
    
    public function getReason()
    {
        if ($this->error) {
            return $this->error;
        }
        
        return $this->response;
    }
    
    public function getRequest()
    {
        return new class($this->endpoint) {
            private $endpoint;
            
            public function __construct($endpoint)
            {
                $this->endpoint = $endpoint;
            }
            
            public function getUri()
            {
                return new class($this->endpoint) {
                    private $endpoint;
                    
                    public function __construct($endpoint)
                    {
                        $this->endpoint = $endpoint;
                    }
                    
                    public function __toString()
                    {
                        return $this->endpoint;
                    }
                };
            }
        };
    }
}
