<?php

namespace AliyunSLS\Models\Request;
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
class RetryShipperTasksRequest extends Request
{
    private $shipperName;
    private $logStore;
    private $taskLists;

    /**
     * @return mixed
     */
    public function getTaskLists()
    {
        return $this->taskLists;
    }

    /**
     * @param mixed $taskLists
     */
    public function setTaskLists($taskLists)
    {
        $this->taskLists = $taskLists;
    }

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