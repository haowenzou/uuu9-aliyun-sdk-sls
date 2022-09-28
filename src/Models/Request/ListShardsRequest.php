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
class ListShardsRequest extends Request
{

    private $logstore;

    /**
     * ListShardsRequest Constructor
     *
     */
    public function __construct($project, $logstore)
    {
        parent::__construct($project);
        $this->logstore = $logstore;
    }

    public function getLogstore()
    {
        return $this->logstore;
    }

    public function setLogstore($logstore)
    {
        $this->logstore = $logstore;
    }


}
