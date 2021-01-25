# dbapi
PHP简易型实用型避坑数据库接口(包括数据库操作、请求JSON、短信发送接口、微信小程序二维码生成)，实用性超级强

首先require或者include该文件
    public function conn(); //连接数据库 ; 调用该数据库请用$conn=C::conn(); 即可调用
    public function get($tablename,$fileds="*",$where); //查询数据库 所有的where都是字符串，都是数据库where后的内容
    public function insert($tablename,$arr=["point"=>"value"]); //插入数据库
    public function update($tablename,$arr=["point"=>"value"],$where); //更新数据库
    public function delete($tablename,$where); //删除数据库
    public function api_post($url); //API JSON请求接口
    public function smssend($tel,$content); //短信发送指定手机及内容
    public function http_post_json_qcoder($url,$data); //微信请求小程序二维码特定请求
    public function getqcoder($path);//调用时，请到调用界面无任何html元素直接调用即可
