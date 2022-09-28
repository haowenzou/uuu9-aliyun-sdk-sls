<?php

namespace AliyunSLS\Models\Request;
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */


/**
 *
 *
 * @author log service dev
 */
class CreateConfigRequest extends Request
{

    private $config;

    /**
     * CreateConfigRequest Constructor
     *
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;

    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

}
