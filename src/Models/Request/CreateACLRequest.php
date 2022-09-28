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
class CreateACLRequest extends Request
{

    private $acl;

    /**
     * CreateACLRequest Constructor
     *
     */
    public function __construct($acl = null)
    {
        $this->acl = $acl;
    }

    public function getAcl()
    {
        return $this->acl;
    }

    public function setAcl($acl)
    {
        $this->acl = $acl;
    }

}
