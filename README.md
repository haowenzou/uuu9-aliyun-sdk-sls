# uuu9-aliyun-sdk-sls
# Aliyun Log Service PHP SDK

## API VERSION

0.6.1

## SDK RELEASE TIME

2018-02-18

## Introduction

阿里云日志服务SDK(www.aliyun.com/product/sls)


### Summary

```php
$client = new Client(endpoint, accessKey, secretKey);

$logItem = new LogItem();
$logItem->setTime(time());
$logItem->setContents(['apilog' => json_encode($record)]);
        
$req = new PutLogsRequest(project, logStore, 'topic', null, [$logItem]);
$client->putLogs($req);
```

## Environment Requirement
PHP >=7.0.12

