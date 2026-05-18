<?php

namespace App\ThirdParty\WebPush;

class Subscription {
    private $subscription;
    
    private function __construct($subscription)
    {
        $this->subscription = $subscription;
    }
    
    public static function create($subscriptionData)
    {
        return new self($subscriptionData);
    }
    
    public function getSubscription()
    {
        return $this->subscription;
    }
    
    public function getEndpoint()
    {
        return $this->subscription['endpoint'];
    }
    
    public function getKeys()
    {
        return $this->subscription['keys'];
    }
}
