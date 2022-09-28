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
class UpdateMachineGroupRequest extends Request
{

    private $machineGroup;

    /**
     * UpdateMachineGroupRequest Constructor
     *
     */
    public function __construct($machineGroup)
    {
        $this->machineGroup = $machineGroup;
    }

    public function getMachineGroup()
    {
        return $this->machineGroup;
    }

    public function setMachineGroup($machineGroup)
    {
        $this->machineGroup = $machineGroup;
    }


}
