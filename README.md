# yisu-sms
一个亿速云短信接口的PHP类库，可用于发送短信，查询短信接收状态信息

## 安装
```
composer require yisu-cloud/sms
```

## 使用
### config
|配置|类型|默认|必须配置|说明|
|-|-|-|-|-|
|version|string|`v1`|N|短信接口版本|
|domain|string|`api.yisu.com`|N|短信服务器域名|
|security|bool|true|N|是否使用https请求协议|
|access_id|string||Y|你的亿速云accessId|
|access_secret|string||Y|你的亿速云accessSecrect|

### 发送短信
```
use yisu\sms\YisuSms;

$config = [
    'access_id'=>'your yisuyun accessId',
    'access_secret'=>'your yisuyun accessSecret'
];
$sms = new YisuSms($config);
$res = $sms->send("1830668xxxx", 100021, ['name'=>'Lin', 'code'=>123125]);
```
### 短信发送记录查询
```
use yisu\sms\YisuSms;

$config = [
    'access_id'=>'your yisuyun accessId',
    'access_secret'=>'your yisuyun accessSecret'
];
$sms = new YisuSms($config);
$res = $sms->query('1830668xxxx', '2020-05-01', '2020-05-05', 1, 50);
```

详细接口文档请参考： [https://www.yisu.com/help/index_157_209.html](https://www.yisu.com/help/index_157_209.html '接口文档')