<?php

namespace AliyunSLS\Models\Request;
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */


/**
 * The request used to list logstore from log service.
 *
 * @author log service dev
 */
class ListLogstoresRequest extends Request
{

    /**
     * ListLogstoresRequest constructor
     *
     * @param string $project project name
     */
    public function __construct($project = null)
    {
        parent::__construct($project);
    }
}
