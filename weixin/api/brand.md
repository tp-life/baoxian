### 获取品牌与型号

* 请求接口 ```http://url/mweixin/brand/index```
* 请求方式 ```POST```
* 请求参数
<table cellspacing=0 cellpadding=0>
  <tr>
    <td>序号</td>
    <td>参数名</td>
    <td>默认值</td>
    <td>类型</td>
    <td>是否必须</td>
    <td>说明</td>
  </tr>
  <tr>
    <td>1</td>
    <td>pid</td>
    <td>0</td>
    <td>int</td>
    <td>否</td>
    <td>上层品牌ID，默认为0，返回所有品牌数据</td>
  </tr>
</table>

* 请求示例 ```http://api.baoxian.com/mweixin/brand/index?token=A1E0D199BE6A77E6E7E98EF7E55828A1```
* 返回值
<table cellspacing=0 cellpadding=0>
  <tr>
    <td>序号</td>
    <td>参数名</td>
    <td>类型</td>
    <td>说明</td>
  </tr>
  <tr>
    <td>1</td>
    <td>id</td>
    <td>int</td>
    <td>品牌型号具体ID</td>
  </tr>
  <tr>
    <td>2</td>
    <td>model_name</td>
    <td>string</td>
    <td>品牌型号名称</td>
  </tr>
  <tr>
    <td>3</td>
    <td>parent_id</td>
    <td>int</td>
    <td>上层品牌模型  为0 表示 品牌</td>
  </tr>
  <tr>
    <td>4</td>
    <td>first_word</td>
    <td>string</td>
    <td> 品牌首字母</td>
  </tr>
  <tr>
    <td>5</td>
    <td>sort</td>
    <td>int</td>
    <td>排序值</td>
  </tr>
</table>

*   返回示例
```JSON
{
"code": 200,
"message": "Success",
"data": [
{
"id": "208",
"model_name": "三星",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "214",
"model_name": "苹果",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "215",
"model_name": "HTC",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "222",
"model_name": "TCL",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "235",
"model_name": "海信",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "366",
"model_name": "小米",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "367",
"model_name": "华为",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "370",
"model_name": "魅族",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "375",
"model_name": "中兴",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "376",
"model_name": "OPPO",
"parent_id": "0",
"first_word": "",
"sort": "255"
},
{
"id": "378",
"model_name": "VIVO",
"parent_id": "0",
"first_word": "",
"sort": "255"
}
]
}
```
