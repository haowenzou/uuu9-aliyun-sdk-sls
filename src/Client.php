<?php

namespace AliyunSLS;

use AliyunSLS\Log\Log;
use AliyunSLS\Log\LogGroup;
use AliyunSLS\Models\Request\ApplyConfigToMachineGroupRequest;
use AliyunSLS\Models\Request\BatchGetLogsRequest;
use AliyunSLS\Models\Request\CreateACLRequest;
use AliyunSLS\Models\Request\CreateConfigRequest;
use AliyunSLS\Models\Request\CreateLogstoreRequest;
use AliyunSLS\Models\Request\CreateMachineGroupRequest;
use AliyunSLS\Models\Request\CreateShipperRequest;
use AliyunSLS\Models\Request\DeleteACLRequest;
use AliyunSLS\Models\Request\DeleteConfigRequest;
use AliyunSLS\Models\Request\DeleteLogstoreRequest;
use AliyunSLS\Models\Request\DeleteMachineGroupRequest;
use AliyunSLS\Models\Request\DeleteShardRequest;
use AliyunSLS\Models\Request\DeleteShipperRequest;
use AliyunSLS\Models\Request\GetACLRequest;
use AliyunSLS\Models\Request\GetConfigRequest;
use AliyunSLS\Models\Request\GetCursorRequest;
use AliyunSLS\Models\Request\GetHistogramsRequest;
use AliyunSLS\Models\Request\GetLogsRequest;
use AliyunSLS\Models\Request\GetMachineGroupRequest;
use AliyunSLS\Models\Request\GetMachineRequest;
use AliyunSLS\Models\Request\GetProjectLogsRequest;
use AliyunSLS\Models\Request\GetShipperConfigRequest;
use AliyunSLS\Models\Request\GetShipperTasksRequest;
use AliyunSLS\Models\Request\ListACLsRequest;
use AliyunSLS\Models\Request\ListConfigsRequest;
use AliyunSLS\Models\Request\ListLogstoresRequest;
use AliyunSLS\Models\Request\ListMachineGroupsRequest;
use AliyunSLS\Models\Request\ListShardsRequest;
use AliyunSLS\Models\Request\ListShipperRequest;
use AliyunSLS\Models\Request\ListTopicsRequest;
use AliyunSLS\Models\Request\MergeShardsRequest;
use AliyunSLS\Models\Request\PutLogsRequest;
use AliyunSLS\Models\Request\RemoveConfigFromMachineGroupRequest;
use AliyunSLS\Models\Request\RetryShipperTasksRequest;
use AliyunSLS\Models\Request\SplitShardRequest;
use AliyunSLS\Models\Request\UpdateACLRequest;
use AliyunSLS\Models\Request\UpdateConfigRequest;
use AliyunSLS\Models\Request\UpdateLogstoreRequest;
use AliyunSLS\Models\Request\UpdateMachineGroupRequest;
use AliyunSLS\Models\Request\UpdateShipperRequest;
use AliyunSLS\Models\Response\ApplyConfigToMachineGroupResponse;
use AliyunSLS\Models\Response\BatchGetLogsResponse;
use AliyunSLS\Models\Response\CreateACLResponse;
use AliyunSLS\Models\Response\CreateConfigResponse;
use AliyunSLS\Models\Response\CreateLogstoreResponse;
use AliyunSLS\Models\Response\CreateMachineGroupResponse;
use AliyunSLS\Models\Response\CreateShipperResponse;
use AliyunSLS\Models\Response\DeleteACLResponse;
use AliyunSLS\Models\Response\DeleteConfigResponse;
use AliyunSLS\Models\Response\DeleteLogstoreResponse;
use AliyunSLS\Models\Response\DeleteMachineGroupResponse;
use AliyunSLS\Models\Response\DeleteShardResponse;
use AliyunSLS\Models\Response\DeleteShipperResponse;
use AliyunSLS\Models\Response\GetACLResponse;
use AliyunSLS\Models\Response\GetConfigResponse;
use AliyunSLS\Models\Response\GetCursorResponse;
use AliyunSLS\Models\Response\GetHistogramsResponse;
use AliyunSLS\Models\Response\GetLogsResponse;
use AliyunSLS\Models\Response\GetMachineGroupResponse;
use AliyunSLS\Models\Response\GetMachineResponse;
use AliyunSLS\Models\Response\GetShipperConfigResponse;
use AliyunSLS\Models\Response\GetShipperTasksResponse;
use AliyunSLS\Models\Response\ListACLsResponse;
use AliyunSLS\Models\Response\ListConfigsResponse;
use AliyunSLS\Models\Response\ListLogstoresResponse;
use AliyunSLS\Models\Response\ListMachineGroupsResponse;
use AliyunSLS\Models\Response\ListShardsResponse;
use AliyunSLS\Models\Response\ListShipperResponse;
use AliyunSLS\Models\Response\ListTopicsResponse;
use AliyunSLS\Models\Response\PutLogsResponse;
use AliyunSLS\Models\Response\RemoveConfigFromMachineGroupResponse;
use AliyunSLS\Models\Response\RetryShipperTasksResponse;
use AliyunSLS\Models\Response\UpdateACLResponse;
use AliyunSLS\Models\Response\UpdateConfigResponse;
use AliyunSLS\Models\Response\UpdateLogstoreResponse;
use AliyunSLS\Models\Response\UpdateMachineGroupResponse;
use AliyunSLS\Models\Response\UpdateShipperResponse;

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
if (!defined('API_VERSION'))
    define('API_VERSION', '0.6.0');
if (!defined('USER_AGENT'))
    define('USER_AGENT', 'log-php-sdk-v-0.6.0');

/**
 * Client class is the main class in the SDK. It can be used to
 * communicate with LOG server to put/get data.
 *
 * @author log_dev
 */
class Client
{

    /**
     * @var string aliyun accessKey
     */
    protected $accessKey;

    /**
     * @var string aliyun accessKeyId
     */
    protected $accessKeyId;

    /**
     * @var string aliyun sts token
     */
    protected $stsToken;

    /**
     * @var string LOG endpoint
     */
    protected $endpoint;

    /**
     * @var string Check if the host if row ip.
     */
    protected $isRowIp;

    /**
     * @var integer Http send port. The dafault value is 80.
     */
    protected $port;

    /**
     * @var string log sever host.
     */
    protected $logHost;

    /**
     * @var string the local machine ip address.
     */
    protected $source;

    /**
     * Client constructor.
     * @param $endpoint LOG host name, for example, http://cn-hangzhou.sls.aliyuncs.com
     * @param $accessKeyId
     * @param $accessKey
     * @param string $token
     */
    public function __construct($endpoint, $accessKeyId, $accessKey, $token = "")
    {
        $this->setEndpoint($endpoint); // set $this->logHost
        $this->accessKeyId = $accessKeyId;
        $this->accessKey = $accessKey;
        $this->stsToken = $token;
        $this->source = Util::getLocalIp();
    }

    /**
     * @param $endpoint
     */
    private function setEndpoint($endpoint)
    {
        $pos = strpos($endpoint, "://");
        if ($pos !== false) { // be careful, !==
            $pos += 3;
            $endpoint = substr($endpoint, $pos);
        }
        $pos = strpos($endpoint, "/");
        if ($pos !== false) // be careful, !==
            $endpoint = substr($endpoint, 0, $pos);
        $pos = strpos($endpoint, ':');
        if ($pos !== false) { // be careful, !==
            $this->port = ( int )substr($endpoint, $pos + 1);
            $endpoint = substr($endpoint, 0, $pos);
        } else
            $this->port = 80;
        $this->isRowIp = Util::isIp($endpoint);
        $this->logHost = $endpoint;
        $this->endpoint = $endpoint . ':' . ( string )$this->port;
    }


    /**
     * Put logs to Log Service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     * @param PutLogsRequest $request
     * @param array $curlOptions
     * @return PutLogsResponse
     * @throws Exception
     */
    public function putLogs(PutLogsRequest $request, $curlOptions = array())
    {
        if (count($request->getLogitems()) > 4096)
            throw new Exception ('InvalidLogSize', "logItems' length exceeds maximum limitation: 4096 lines.");

        $logGroup = new LogGroup();
//        $topic = $request->getTopic() !== null ? $request->getTopic() : '';
        $logGroup->setTopic($request->getTopic());
        $source = $request->getSource();

        if (!$source)
            $source = $this->source;
        $logGroup->setSource($source);
        $logitems = $request->getLogitems();
        foreach ($logitems as $logItem) {
            $log = new Log();
            $log->setTime($logItem->getTime());
            $content = $logItem->getContents();
            foreach ($content as $key => $value) {
                $content = new LogContent();
                $content->setKey($key);
                $content->setValue($value);
                $log->addContents($content);
            }

            $logGroup->addLogs($log);
        }

        $body = Util::toBytes($logGroup);
        unset ($logGroup);

        $bodySize = strlen($body);
        if ($bodySize > 3 * 1024 * 1024) // 3 MB
            throw new Exception ('InvalidLogSize', "logItems' size exceeds maximum limitation: 3 MB.");
        $params = array();
        $headers = array();
        $headers ["x-log-bodyrawsize"] = $bodySize;
        $headers ['x-log-compresstype'] = 'deflate';
        $headers ['Content-Type'] = 'application/x-protobuf';
        $body = gzcompress($body, 6);

        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $shardKey = $request->getShardKey();
        $resource = "/logstores/" . $logstore . ($shardKey == null ? "/shards/lb" : "/shards/route");
        if ($shardKey)
            $params["key"] = $shardKey;
        list ($resp, $header) = $this->send("POST", $project, $body, $resource, $params, $headers, $curlOptions);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $this->parseToJson($resp, $requestId);
        return new PutLogsResponse($header);
    }

    /**
     * @param $method
     * @param $project
     * @param $body
     * @param $resource
     * @param $params
     * @param $headers
     * @param $curlOptions
     * @return array
     * @throws Exception
     */
    private function send($method, $project, $body, $resource, $params, $headers, $curlOptions = array())
    {
        if ($body) {
            $headers ['Content-Length'] = strlen($body);
            if (isset($headers ["x-log-bodyrawsize"]) == false)
                $headers ["x-log-bodyrawsize"] = 0;
            $headers ['Content-MD5'] = Util::calMD5($body);
        } else {
            $headers ['Content-Length'] = 0;
            $headers ["x-log-bodyrawsize"] = 0;
            $headers ['Content-Type'] = ''; // If not set, http request will add automatically.
        }

        $headers ['x-log-apiversion'] = API_VERSION;
        $headers ['x-log-signaturemethod'] = 'hmac-sha1';
        if (strlen($this->stsToken) > 0)
            $headers ['x-acs-security-token'] = $this->stsToken;
        if (is_null($project)) $headers ['Host'] = $this->logHost;
        else $headers ['Host'] = "$project.$this->logHost";
        $headers ['Date'] = $this->GetGMT();
        $signature = Util::getRequestAuthorization($method, $resource, $this->accessKey, $this->stsToken, $params, $headers);
        $headers ['Authorization'] = "LOG $this->accessKeyId:$signature";

        $url = $resource;
        if ($params)
            $url .= '?' . Util::urlEncode($params);
        if ($this->isRowIp)
            $url = "http://$this->endpoint$url";
        else {
            if (is_null($project))
                $url = "http://$this->endpoint$url";
            else  $url = "http://$project.$this->endpoint$url";
        }
        return $this->sendRequest($method, $url, $body, $headers, $curlOptions);
    }

    /**
     * GMT format time string.
     *
     * @return string
     */
    protected function getGMT()
    {
        return gmdate('D, d M Y H:i:s') . ' GMT';
    }

    /**
     * @param $method
     * @param $url
     * @param $body
     * @param $headers
     * @param $curlOptions
     * @return array
     * @throws Exception
     */
    private function sendRequest($method, $url, $body, $headers, $curlOptions = array())
    {
        try {
            list ($responseCode, $header, $resBody) =
                $this->getHttpResponse($method, $url, $body, $headers, $curlOptions);
        } catch (\Exception $ex) {
            throw new Exception ($ex->getMessage(), $ex->__toString());
        }

        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';

        if ($responseCode == 200) {
            return array($resBody, $header);
        } else {
            $exJson = $this->parseToJson($resBody, $requestId);
            if (isset($exJson ['error_code']) && isset($exJson ['error_message'])) {
                throw new Exception ($exJson ['error_code'],
                    $exJson ['error_message'], $requestId);
            } else {
                if ($exJson) {
                    $exJson = ' The return json is ' . json_encode($exJson);
                } else {
                    $exJson = '';
                }
                throw new Exception ('RequestError',
                    "Request is failed. Http code is $responseCode.$exJson", $requestId);
            }
        }
    }

    /**
     * @param $method
     * @param $url
     * @param $body
     * @param $headers
     * @param $curlOptions
     * @return array
     * @throws RequestCoreException
     */
    protected function getHttpResponse($method, $url, $body, $headers, $curlOptions = array())
    {
        $request = new RequestCore($url);
        foreach ($headers as $key => $value)
            $request->add_header($key, $value);
        $request->set_method($method);
        $request->set_useragent(USER_AGENT);
        if ($method == "POST" || $method == "PUT")
            $request->set_body($body);
        if (!empty($curlOptions)) {
            $request->set_curlopts($curlOptions);
        }


        $request->send_request();
        $response = array();
        $response [] = ( int )$request->get_response_code();
        $response [] = $request->get_response_header();
        $response [] = $request->get_response_body();
        return $response;
    }

    /**
     * Decodes a JSON string to a JSON Object.
     * Unsuccessful decode will cause an Aliyun_Log_Exception.
     *
     * @param $resBody
     * @param $requestId
     * @return mixed|null
     * @throws Exception
     */
    protected function parseToJson($resBody, $requestId)
    {
        if (!$resBody)
            return NULL;

        $result = json_decode($resBody, true);
        if ($result === NULL) {
            throw new Exception ('BadResponse', "Bad format,not json: $resBody", $requestId);
        }
        return $result;
    }

    /**
     * create shipper service
     * @param CreateShipperRequest $request
     * @return CreateShipperResponse
     * @throws Exception
     */
    public function createShipper(CreateShipperRequest $request)
    {
        $headers = array();
        $params = array();
        $resource = "/logstores/" . $request->getLogStore() . "/shipper";
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $headers["Content-Type"] = "application/json";

        $body = array(
            "shipperName" => $request->getShipperName(),
            "targetType" => $request->getTargetType(),
            "targetConfiguration" => $request->getTargetConfigration()
        );
        $body_str = json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp, $header) = $this->send("POST", $project, $body_str, $resource, $params, $headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new CreateShipperResponse($resp, $header);
    }

    /**
     * create shipper service
     * @param UpdateShipperRequest $request
     * @return UpdateShipperResponse
     * @throws Exception
     */
    public function updateShipper(UpdateShipperRequest $request)
    {
        $headers = array();
        $params = array();
        $resource = "/logstores/" . $request->getLogStore() . "/shipper/" . $request->getShipperName();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $headers["Content-Type"] = "application/json";

        $body = array(
            "shipperName" => $request->getShipperName(),
            "targetType" => $request->getTargetType(),
            "targetConfiguration" => $request->getTargetConfigration()
        );
        $body_str = json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp, $header) = $this->send("PUT", $project, $body_str, $resource, $params, $headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new UpdateShipperResponse($resp, $header);
    }

    /**
     * get shipper tasks list, max 48 hours duration supported
     * @param GetShipperTasksRequest $request
     * @return GetShipperTasksResponse
     * @throws Exception
     */
    public function getShipperTasks(GetShipperTasksRequest $request)
    {
        $headers = array();
        $params = array(
            'from' => $request->getStartTime(),
            'to' => $request->getEndTime(),
            'status' => $request->getStatusType(),
            'offset' => $request->getOffset(),
            'size' => $request->getSize()
        );
        $resource = "/logstores/" . $request->getLogStore() . "/shipper/" . $request->getShipperName() . "/tasks";
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("GET", $project, null, $resource, $params, $headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new GetShipperTasksResponse($resp, $header);
    }

    /**
     * retry shipper tasks list by task ids
     * @param RetryShipperTasksRequest $request
     * @return RetryShipperTasksResponse
     * @throws Exception
     */
    public function retryShipperTasks(RetryShipperTasksRequest $request)
    {
        $headers = array();
        $params = array();
        $resource = "/logstores/" . $request->getLogStore() . "/shipper/" . $request->getShipperName() . "/tasks";
        $project = $request->getProject() !== null ? $request->getProject() : '';

        $headers["Content-Type"] = "application/json";
        $body = $request->getTaskLists();
        $body_str = json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp, $header) = $this->send("PUT", $project, $body_str, $resource, $params, $headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new RetryShipperTasksResponse($resp, $header);
    }

    /**
     * delete shipper service
     * @param DeleteShipperRequest $request
     * @return DeleteShipperResponse
     * @throws Exception
     */
    public function deleteShipper(DeleteShipperRequest $request)
    {
        $headers = array();
        $params = array();
        $resource = "/logstores/" . $request->getLogStore() . "/shipper/" . $request->getShipperName();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("DELETE", $project, null, $resource, $params, $headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new DeleteShipperResponse($resp, $header);
    }

    /**
     * get shipper config service
     * @param GetShipperConfigRequest $request
     * @return GetShipperConfigResponse
     * @throws Exception
     */
    public function getShipperConfig(GetShipperConfigRequest $request)
    {
        $headers = array();
        $params = array();
        $resource = "/logstores/" . $request->getLogStore() . "/shipper/" . $request->getShipperName();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("GET", $project, null, $resource, $params, $headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new GetShipperConfigResponse($resp, $header);
    }

    /**
     * list shipper service
     * @param ListShipperRequest $request
     * @return ListShipperResponse
     * @throws Exception
     */
    public function listShipper(ListShipperRequest $request)
    {
        $headers = array();
        $params = array();
        $resource = "/logstores/" . $request->getLogStore() . "/shipper";
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";

        list($resp, $header) = $this->send("GET", $project, null, $resource, $params, $headers);
        $requestId = isset($header['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new ListShipperResponse($resp, $header);
    }

    /**
     * create logstore
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param CreateLogstoreRequest $request the CreateLogStore request parameters class.
     * @return CreateLogstoreResponse
     * @throws Exception
     */
    public function createLogstore(CreateLogstoreRequest $request)
    {
        $headers = array();
        $params = array();
        $resource = '/logstores';
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $headers["x-log-bodyrawsize"] = 0;
        $headers["Content-Type"] = "application/json";
        $body = array(
            "logstoreName" => $request->getLogstore(),
            "ttl" => (int)($request->getTtl()),
            "shardCount" => (int)($request->getShardCount())
        );
        $body_str = json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp, $header) = $this->send("POST", $project, $body_str, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new CreateLogstoreResponse($resp, $header);
    }


    /**
     * update logstore
     * @param UpdateLogstoreRequest $request the UpdateLogStore request parameters class.
     * @return UpdateLogstoreResponse
     * @throws Exception
     */
    public function updateLogstore(UpdateLogstoreRequest $request)
    {
        $headers = array();
        $params = array();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $headers["Content-Type"] = "application/json";
        $body = array(
            "logstoreName" => $request->getLogstore(),
            "ttl" => (int)($request->getTtl()),
            "shardCount" => (int)($request->getShardCount())
        );
        $resource = '/logstores/' . $request->getLogstore();
        $body_str = json_encode($body);
        $headers["x-log-bodyrawsize"] = strlen($body_str);
        list($resp, $header) = $this->send("PUT", $project, $body_str, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new UpdateLogstoreResponse($resp, $header);
    }


    /**
     * List all logstores of requested project.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     * @param ListLogstoresRequest $request
     * @return ListLogstoresResponse
     * @throws Exception
     */
    public function listLogstores(ListLogstoresRequest $request)
    {
        $headers = array();
        $params = array();
        $resource = '/logstores';
        $project = $request->getProject() !== null ? $request->getProject() : '';
        list ($resp, $header) = $this->send("GET", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new ListLogstoresResponse($resp, $header);
    }


    /**
     * Delete logstore
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     * @param DeleteLogstoreRequest $request
     * @return DeleteLogstoreResponse
     * @throws Exception
     */
    public function deleteLogstore(DeleteLogstoreRequest $request)
    {
        $headers = array();
        $params = array();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $logstore = $request->getLogstore() != null ? $request->getLogstore() : "";
        $resource = "/logstores/$logstore";
        list ($resp, $header) = $this->send("DELETE", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new DeleteLogstoreResponse($resp, $header);
    }


    /**
     * List all topics in a logstore.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param ListTopicsRequest $request
     * @return ListTopicsResponse
     * @throws Exception
     */
    public function listTopics(ListTopicsRequest $request)
    {
        $headers = array();
        $params = array();
        if ($request->getToken() !== null)
            $params ['token'] = $request->getToken();
        if ($request->getLine() !== null)
            $params ['line'] = $request->getLine();
        $params ['type'] = 'topic';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $resource = "/logstores/$logstore";
        list ($resp, $header) = $this->send("GET", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new ListTopicsResponse($resp, $header);
    }


    /**
     * Get histograms of requested query from log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param GetHistogramsRequest $request
     * @return GetHistogramsResponse
     * @throws Exception
     */
    public function getHistograms(GetHistogramsRequest $request)
    {
        $ret = $this->getHistogramsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new GetHistogramsResponse($resp, $header);
    }

    /**
     * Get histograms of requested query from log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param GetHistogramsRequest $request the GetHistograms request parameters class.
     * @throws Exception
     * @return array(json body, http header)
     */
    public function getHistogramsJson(GetHistogramsRequest $request)
    {
        $headers = array();
        $params = array();
        if ($request->getTopic() !== null)
            $params ['topic'] = $request->getTopic();
        if ($request->getFrom() !== null)
            $params ['from'] = $request->getFrom();
        if ($request->getTo() !== null)
            $params ['to'] = $request->getTo();
        if ($request->getQuery() !== null)
            $params ['query'] = $request->getQuery();
        $params ['type'] = 'histogram';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $resource = "/logstores/$logstore";
        list ($resp, $header) = $this->send("GET", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return array($resp, $header);
    }

    /**
     * Get logs from Log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param GetLogsRequest $request the GetLogs request parameters class.
     * @throws Exception
     * @return GetLogsResponse
     */
    public function getLogs(GetLogsRequest $request)
    {
        $ret = $this->getLogsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new GetLogsResponse($resp, $header);
    }

    /**
     * Get logs from Log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param GetLogsRequest $request the GetLogs request parameters class.
     * @throws Exception
     * @return array(json body, http header)
     */
    public function getLogsJson(GetLogsRequest $request)
    {
        $headers = array();
        $params = array();
        if ($request->getTopic() !== null)
            $params ['topic'] = $request->getTopic();
        if ($request->getFrom() !== null)
            $params ['from'] = $request->getFrom();
        if ($request->getTo() !== null)
            $params ['to'] = $request->getTo();
        if ($request->getQuery() !== null)
            $params ['query'] = $request->getQuery();
        $params ['type'] = 'log';
        if ($request->getLine() !== null)
            $params ['line'] = $request->getLine();
        if ($request->getOffset() !== null)
            $params ['offset'] = $request->getOffset();
        if ($request->getOffset() !== null)
            $params ['reverse'] = $request->getReverse() ? 'true' : 'false';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $resource = "/logstores/$logstore";
        list ($resp, $header) = $this->send("GET", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return array($resp, $header);
        //return new GetLogsResponse ( $resp, $header );
    }

    /**
     * Get logs from Log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param GetProjectLogsRequest $request the GetLogs request parameters class.
     * @throws Exception
     * @return GetLogsResponse
     */
    public function getProjectLogs(GetProjectLogsRequest $request)
    {
        $ret = $this->getProjectLogsJson($request);
        $resp = $ret[0];
        $header = $ret[1];
        return new GetLogsResponse($resp, $header);
    }

    /**
     * Get logs from Log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param GetProjectLogsRequest $request the GetLogs request parameters class.
     * @throws Exception
     * @return array(json body, http header)
     */
    public function getProjectLogsJson(GetProjectLogsRequest $request)
    {
        $headers = array();
        $params = array();
        if ($request->getQuery() !== null)
            $params ['query'] = $request->getQuery();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $resource = "/logs";
        list ($resp, $header) = $this->send("GET", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return array($resp, $header);
        //return new GetLogsResponse ( $resp, $header );
    }

    /**
     * Get logs from Log service with shardid conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param BatchGetLogsRequest $request the BatchGetLogs request parameters class.
     * @throws Exception
     * @return BatchGetLogsResponse
     */
    public function batchGetLogs(BatchGetLogsRequest $request)
    {
        $params = array();
        $headers = array();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $shardId = $request->getShardId() !== null ? $request->getShardId() : '';
        if ($request->getCount() !== null)
            $params['count'] = $request->getCount();
        if ($request->getCursor() !== null)
            $params['cursor'] = $request->getCursor();
        if ($request->getEndCursor() !== null)
            $params['end_cursor'] = $request->getEndCursor();
        $params['type'] = 'log';
        $headers['Accept-Encoding'] = 'gzip';
        $headers['accept'] = 'application/x-protobuf';

        $resource = "/logstores/$logstore/shards/$shardId";
        list($resp, $header) = $this->send("GET", $project, NULL, $resource, $params, $headers);
        //$resp is a byteArray
        $resp = gzuncompress($resp);
        if ($resp === false) $resp = new LogGroupList();

        else {
            $resp = new LogGroupList($resp);
        }
        return new BatchGetLogsResponse($resp, $header);
    }

    /**
     * List Shards from Log service with Project and logstore conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param ListShardsRequest $request the ListShards request parameters class.
     * @throws Exception
     * @return ListShardsResponse
     */
    public function listShards(ListShardsRequest $request)
    {
        $params = array();
        $headers = array();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';

        $resource = '/logstores/' . $logstore . '/shards';
        list($resp, $header) = $this->send("GET", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new ListShardsResponse($resp, $header);
    }

    /**
     * split a shard into two shards  with Project and logstore and shardId and midHash conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param SplitShardRequest $request the SplitShard request parameters class.
     * @throws Exception
     * @return ListShardsResponse
     */
    public function splitShard(SplitShardRequest $request)
    {
        $params = array();
        $headers = array();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $shardId = $request->getShardId() !== null ? $request->getShardId() : -1;
        $midHash = $request->getMidHash() != null ? $request->getMidHash() : "";

        $resource = '/logstores/' . $logstore . '/shards/' . $shardId;
        $params["action"] = "split";
        $params["key"] = $midHash;
        list($resp, $header) = $this->send("POST", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new ListShardsResponse($resp, $header);
    }

    /**
     * merge two shards into one shard with Project and logstore and shardId and conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param MergeShardsRequest $request the MergeShards request parameters class.
     * @throws Exception
     * @return ListShardsResponse
     */
    public function MergeShards(MergeShardsRequest $request)
    {
        $params = array();
        $headers = array();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $shardId = $request->getShardId() != null ? $request->getShardId() : -1;

        $resource = '/logstores/' . $logstore . '/shards/' . $shardId;
        $params["action"] = "merge";
        list($resp, $header) = $this->send("POST", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new ListShardsResponse ($resp, $header);
    }


    /**
     * delete a read only shard with Project and logstore and shardId conditions.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     * @param DeleteShardRequest $request
     * @return DeleteShardResponse
     * @throws Exception
     */
    public function DeleteShard(DeleteShardRequest $request)
    {
        $params = array();
        $headers = array();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $shardId = $request->getShardId() != null ? $request->getShardId() : -1;

        $resource = '/logstores/' . $logstore . '/shards/' . $shardId;
        list($resp, $header) = $this->send("DELETE", $project, NULL, $resource, $params, $headers);
//        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        return new DeleteShardResponse($header);
    }

    /**
     * Get cursor from Log service.
     * Unsuccessful opertaion will cause an Aliyun_Log_Exception.
     *
     * @param GetCursorRequest $request the GetCursor request parameters class.
     * @throws Exception
     * @return GetCursorResponse
     */
    public function getCursor(GetCursorRequest $request)
    {
        $params = array();
        $headers = array();
        $project = $request->getProject() !== null ? $request->getProject() : '';
        $logstore = $request->getLogstore() !== null ? $request->getLogstore() : '';
        $shardId = $request->getShardId() !== null ? $request->getShardId() : '';
        $mode = $request->getMode() !== null ? $request->getMode() : '';
        $fromTime = $request->getFromTime() !== null ? $request->getFromTime() : -1;

        if ((empty($mode) xor $fromTime == -1) == false) {
            if (!empty($mode))
                throw new Exception ('RequestError', "Request is failed. Mode and fromTime can not be not empty simultaneously");
            else
                throw new Exception ('RequestError', "Request is failed. Mode and fromTime can not be empty simultaneously");
        }
        if (!empty($mode) && strcmp($mode, 'begin') !== 0 && strcmp($mode, 'end') !== 0)
            throw new Exception ('RequestError', "Request is failed. Mode value invalid:$mode");
        if ($fromTime !== -1 && (is_integer($fromTime) == false || $fromTime < 0))
            throw new Exception ('RequestError', "Request is failed. FromTime value invalid:$fromTime");
        $params['type'] = 'cursor';
        if ($fromTime !== -1) $params['from'] = $fromTime;
        else $params['mode'] = $mode;
        $resource = '/logstores/' . $logstore . '/shards/' . $shardId;
        list($resp, $header) = $this->send("GET", $project, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new GetCursorResponse($resp, $header);
    }

    /**
     * @param CreateConfigRequest $request
     * @return CreateConfigResponse
     * @throws Exception
     */
    public function createConfig(CreateConfigRequest $request)
    {
        $params = array();
        $headers = array();
        $body = null;
        if ($request->getConfig() !== null) {
            $body = json_encode($request->getConfig()->toArray());
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/configs';
        list($resp, $header) = $this->send("POST", NULL, $body, $resource, $params, $headers);
        return new CreateConfigResponse($header);
    }

    /**
     * @param UpdateConfigRequest $request
     * @return UpdateConfigResponse
     * @throws Exception
     */
    public function updateConfig(UpdateConfigRequest $request)
    {
        $params = array();
        $headers = array();
        $body = null;
        $configName = '';
        if ($request->getConfig() !== null) {
            $body = json_encode($request->getConfig()->toArray());
            $configName = ($request->getConfig()->getConfigName() !== null) ? $request->getConfig()->getConfigName() : '';
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/configs/' . $configName;
        list($resp, $header) = $this->send("PUT", NULL, $body, $resource, $params, $headers);
        return new UpdateConfigResponse($header);
    }

    /**
     * @param GetConfigRequest $request
     * @return GetConfigResponse
     * @throws Exception
     */
    public function getConfig(GetConfigRequest $request)
    {
        $params = array();
        $headers = array();

        $configName = ($request->getConfigName() !== null) ? $request->getConfigName() : '';

        $resource = '/configs/' . $configName;
        list($resp, $header) = $this->send("GET", NULL, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new GetConfigResponse($resp, $header);
    }

    /**
     * @param DeleteConfigRequest $request
     * @return DeleteConfigResponse
     * @throws Exception
     */
    public function deleteConfig(DeleteConfigRequest $request)
    {
        $params = array();
        $headers = array();
        $configName = ($request->getConfigName() !== null) ? $request->getConfigName() : '';
        $resource = '/configs/' . $configName;
        list($resp, $header) = $this->send("DELETE", NULL, NULL, $resource, $params, $headers);
        return new DeleteConfigResponse($header);
    }

    /**
     * @param ListConfigsRequest $request
     * @return ListConfigsResponse
     * @throws Exception
     */
    public function listConfigs(ListConfigsRequest $request)
    {
        $params = array();
        $headers = array();

        if ($request->getConfigName() !== null) $params['configName'] = $request->getConfigName();
        if ($request->getOffset() !== null) $params['offset'] = $request->getOffset();
        if ($request->getSize() !== null) $params['size'] = $request->getSize();

        $resource = '/configs';
        list($resp, $header) = $this->send("GET", NULL, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new ListConfigsResponse($resp, $header);
    }

    /**
     * @param CreateMachineGroupRequest $request
     * @return CreateMachineGroupResponse
     * @throws Exception
     */
    public function createMachineGroup(CreateMachineGroupRequest $request)
    {
        $params = array();
        $headers = array();
        $body = null;
        if ($request->getMachineGroup() !== null) {
            $body = json_encode($request->getMachineGroup()->toArray());
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups';
        list($resp, $header) = $this->send("POST", NULL, $body, $resource, $params, $headers);

        return new CreateMachineGroupResponse($header);
    }

    /**
     * @param UpdateMachineGroupRequest $request
     * @return UpdateMachineGroupResponse
     * @throws Exception
     */
    public function updateMachineGroup(UpdateMachineGroupRequest $request)
    {
        $params = array();
        $headers = array();
        $body = null;
        $groupName = '';
        if ($request->getMachineGroup() !== null) {
            $body = json_encode($request->getMachineGroup()->toArray());
            $groupName = ($request->getMachineGroup()->getGroupName() !== null) ? $request->getMachineGroup()->getGroupName() : '';
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups/' . $groupName;
        list($resp, $header) = $this->send("PUT", NULL, $body, $resource, $params, $headers);
        return new UpdateMachineGroupResponse($header);
    }

    /**
     * @param GetMachineGroupRequest $request
     * @return GetMachineGroupResponse
     * @throws Exception
     */
    public function getMachineGroup(GetMachineGroupRequest $request)
    {
        $params = array();
        $headers = array();

        $groupName = ($request->getGroupName() !== null) ? $request->getGroupName() : '';

        $resource = '/machinegroups/' . $groupName;
        list($resp, $header) = $this->send("GET", NULL, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new GetMachineGroupResponse($resp, $header);
    }

    /**
     * @param DeleteMachineGroupRequest $request
     * @return DeleteMachineGroupResponse
     * @throws Exception
     */
    public function deleteMachineGroup(DeleteMachineGroupRequest $request)
    {
        $params = array();
        $headers = array();

        $groupName = ($request->getGroupName() !== null) ? $request->getGroupName() : '';
        $resource = '/machinegroups/' . $groupName;
        list($resp, $header) = $this->send("DELETE", NULL, NULL, $resource, $params, $headers);
        return new DeleteMachineGroupResponse($header);
    }

    /**
     * @param ListMachineGroupsRequest $request
     * @return ListMachineGroupsResponse
     * @throws Exception
     */
    public function listMachineGroups(ListMachineGroupsRequest $request)
    {
        $params = array();
        $headers = array();

        if ($request->getGroupName() !== null) $params['groupName'] = $request->getGroupName();
        if ($request->getOffset() !== null) $params['offset'] = $request->getOffset();
        if ($request->getSize() !== null) $params['size'] = $request->getSize();

        $resource = '/machinegroups';
        list($resp, $header) = $this->send("GET", NULL, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);

        return new ListMachineGroupsResponse($resp, $header);
    }

    /**
     * @param ApplyConfigToMachineGroupRequest $request
     * @return ApplyConfigToMachineGroupResponse
     * @throws Exception
     */
    public function applyConfigToMachineGroup(ApplyConfigToMachineGroupRequest $request)
    {
        $params = array();
        $headers = array();
        $configName = $request->getConfigName();
        $groupName = $request->getGroupName();
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups/' . $groupName . '/configs/' . $configName;
        list($resp, $header) = $this->send("PUT", NULL, NULL, $resource, $params, $headers);
        return new ApplyConfigToMachineGroupResponse($header);
    }

    /**
     * @param RemoveConfigFromMachineGroupRequest $request
     * @return RemoveConfigFromMachineGroupResponse
     * @throws Exception
     */
    public function removeConfigFromMachineGroup(RemoveConfigFromMachineGroupRequest $request)
    {
        $params = array();
        $headers = array();
        $configName = $request->getConfigName();
        $groupName = $request->getGroupName();
        $headers ['Content-Type'] = 'application/json';
        $resource = '/machinegroups/' . $groupName . '/configs/' . $configName;
        list($resp, $header) = $this->send("DELETE", NULL, NULL, $resource, $params, $headers);
        return new RemoveConfigFromMachineGroupResponse($header);
    }

    /**
     * @param GetMachineRequest $request
     * @return GetMachineResponse
     * @throws Exception
     */
    public function getMachine(GetMachineRequest $request)
    {
        $params = array();
        $headers = array();

        $uuid = ($request->getUuid() !== null) ? $request->getUuid() : '';

        $resource = '/machines/' . $uuid;
        list($resp, $header) = $this->send("GET", NULL, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new GetMachineResponse($resp, $header);
    }

    /**
     * @param CreateACLRequest $request
     * @return CreateACLResponse
     * @throws Exception
     */
    public function createACL(CreateACLRequest $request)
    {
        $params = array();
        $headers = array();
        $body = null;
        if ($request->getAcl() !== null) {
            $body = json_encode($request->getAcl()->toArray());
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/acls';
        list($resp, $header) = $this->send("POST", NULL, $body, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new CreateACLResponse($resp, $header);
    }

    /**
     * @param UpdateACLRequest $request
     * @return UpdateACLResponse
     * @throws Exception
     */
    public function updateACL(UpdateACLRequest $request)
    {
        $params = array();
        $headers = array();
        $body = null;
        $aclId = '';
        if ($request->getAcl() !== null) {
            $body = json_encode($request->getAcl()->toArray());
            $aclId = ($request->getAcl()->getAclId() !== null) ? $request->getAcl()->getAclId() : '';
        }
        $headers ['Content-Type'] = 'application/json';
        $resource = '/acls/' . $aclId;
        list($resp, $header) = $this->send("PUT", NULL, $body, $resource, $params, $headers);
        return new UpdateACLResponse($header);
    }

    /**
     * @param GetACLRequest $request
     * @return GetACLResponse
     * @throws Exception
     */
    public function getACL(GetACLRequest $request)
    {
        $params = array();
        $headers = array();

        $aclId = ($request->getAclId() !== null) ? $request->getAclId() : '';

        $resource = '/acls/' . $aclId;
        list($resp, $header) = $this->send("GET", NULL, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);

        return new GetACLResponse($resp, $header);
    }

    /**
     * @param DeleteACLRequest $request
     * @return DeleteACLResponse
     * @throws Exception
     */
    public function deleteACL(DeleteACLRequest $request)
    {
        $params = array();
        $headers = array();
        $aclId = ($request->getAclId() !== null) ? $request->getAclId() : '';
        $resource = '/acls/' . $aclId;
        list($resp, $header) = $this->send("DELETE", NULL, NULL, $resource, $params, $headers);
        return new DeleteACLResponse($header);
    }

    /**
     * @param ListACLsRequest $request
     * @return ListACLsResponse
     * @throws Exception
     */
    public function listACLs(ListACLsRequest $request)
    {
        $params = array();
        $headers = array();
        if ($request->getPrincipleId() !== null) $params['principleId'] = $request->getPrincipleId();
        if ($request->getOffset() !== null) $params['offset'] = $request->getOffset();
        if ($request->getSize() !== null) $params['size'] = $request->getSize();

        $resource = '/acls';
        list($resp, $header) = $this->send("GET", NULL, NULL, $resource, $params, $headers);
        $requestId = isset ($header ['x-log-requestid']) ? $header ['x-log-requestid'] : '';
        $resp = $this->parseToJson($resp, $requestId);
        return new ListACLsResponse($resp, $header);
    }

}

