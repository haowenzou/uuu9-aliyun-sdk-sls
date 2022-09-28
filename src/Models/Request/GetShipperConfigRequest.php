<?php

namespace AliyunSLS\Models\Request;
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
class GetShipperConfigRequest extends Request
{
    private $shipperName;
    private $logStore;

    /**
     * @return mixed
     */
    public function getLogStore()
    {
        return $this->logStore;
    }

    /**
     * @param mixed $logStore
     */
    public function setLogStore($logStore)
    {
        $this->logStore = $logStore;
    }


    /**
     * @return mixed
     */
    public function getShipperName()
    {
        return $this->shipperName;
    }

    /**
     * @param mixed $shipperName
     */
    public function setShipperName($shipperName)
    {
        $this->shipperName = $shipperName;
    }

    /**
     * CreateShipperRequest Constructor
     *
     */
    public function __construct($project)
    {
        parent::__construct($project);
    }
}