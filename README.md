## 安全验证
- ### 在config.php文件中添加如下配置
  ```php
  [
    'safety' => [
        'cipher' => false, // 是否开启数据加密
        'sign' => false, // 是否开启签名验证
        'timeout' => 10, // 签名有效时间
        'access_key' => '', // AES密钥
        'public_key' => '', // RSA加密公钥
        'private_key' => '', // RSA解密私钥
    ]
  ]
  ```
- ### 开启加密后请求和响应内容都应为：
  ```php
  [
    'ciphertext' => '', // 密文
    'cipheriv' => '', // 加密初始化向量
  ]
  ```

- ### ciphertext解释
  - 密文使用AES加密
  - 加密成功后返回加密使用的初始化向量和密文

- ### cipheriv解释
  - 使用RSA把AES加密返回的初始化向量加密

- ### 签名规则
  - 把需要签名的数据字典排序
  - 使用accessKey=test&key=val拼接模式，在开头拼接AES密钥
  - 明确忽略的字段和空字段不参与签名
  - 如果开启了数据加密，则签名会使用AES加密一遍拼接后的内容
  - 最后使用md5加密，然后把字符串转为大写