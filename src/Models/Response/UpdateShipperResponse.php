<?php

namespace AliyunSLS\Models\Response;
/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
class UpdateShipperResponse extends Response
{

    /**
     * UpdateShipperResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct($resp, $header)
    {
        parent::__construct($header);
    }
}