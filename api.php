<?php 
/**
 * 简易型实用型避坑数据库接口(包括数据库操作、请求JSON、短信发送接口、微信小程序二维码生成)
 * @package default
 * @author `朱义昆`
 * Wechat Yikunzhu
 */
interface M
{
    public function conn(); //连接数据库
    public function get($tablename,$fileds="*",$where); //查询数据库
    public function insert($tablename,$arr=["point"=>"value"]); //插入数据库
    public function update($tablename,$arr=["point"=>"value"],$where); //更新数据库
    public function delete($tablename,$where); //删除数据库
    public function api_post($url); //API JSON请求接口
    public function smssend($tel,$content); //短信发送指定手机及内容
    public function http_post_json_qcoder($url,$data); //微信请求小程序二维码特定请求
    public function getqcoder($path);//调用时，请到调用界面无任何html元素直接调用即可
}
class C implements M{
    public function conn(){
          $servername = ""; //Dbhost
          $username = ""; //dbuser
          $password = ""; //dbpassword
          $dbname = ""; //dbname
          $conn = new mysqli($servername, $username, $password, $dbname);
          if ($conn->connect_error) {
          die("连接失败: " . $conn->connect_error);
           }
           $conn->query("set names utf8");
          return $conn;
    } //连接数据库
    public function get($tablename,$fileds="*",$where){
        $conn=C::conn();
        if($where==NULL||$where==""){
        $qsql="SELECT $fileds FROM `$tablename`";
        }else{
          $qsql="SELECT $fileds FROM `$tablename` where $where"; 
        }
        $qEof=$conn->query($qsql);
        $i=0;
        while($qq=$qEof->fetch_assoc()){
            $q[$i]=$qq;
            $i++;
        }
        return $q;
    }//查询数据库
    public function insert($tablename,$arr=["point"=>"value"]){
        $conn=C::conn();
        $i=0;
        foreach ($arr as $key => $value){
            $ckey[$i]=$key;
            $cvalue[$i]=$value;
            $i++;
        }
        $ct="";
        $cb="";
        for($j=0;$j<$i;$j++){
            if($j+1==$i){
                $ct.="`".$ckey[$j]."`";
            }else{
                $ct.="`".$ckey[$j]."`,";
            }
        }
         for($j=0;$j<$i;$j++){
         if($j+1==$i){
                $cb.="'".$cvalue[$j]."'";
            }else{
                $cb.="'".$cvalue[$j]."',";
            }
        }
        $isql="INSERT INTO `zuyi_sckfb_cglkfx` ($ct) VALUES ($cb)";
        if($conn->query($isql)){
            return 1; //插入成功
        }else{
            return 0; //插入失败
        }
    } //插入数据库
    public function update($tablename,$arr=["point"=>"value"],$where){
         $conn=C::conn();
        $cub="";
        $i=0;
        foreach ($arr as $key => $value){
          if($i+1==sizeof($arr)){
              $cub.="`$key`='$value'";
          }else{
              $cub.="`$key`='$value',";
          }
        }
        $usql="UPDATE `$tablename` SET $cub WHERE $where";
         if($conn->query($usql)){
            return 1; //更新成功
        }else{
            return 0; //更新失败
        }
    } //更新数据库
    public function delete($tablename,$where){
        $conn=C::conn();
        $dsql="DELETE FROM `$tablename` WHERE $where";
        if($conn->query($dsql)){
            return 1; //删除成功
        }else{
            return 0; //删除失败
        }
        
    } //删除数据库
    public function api_post($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
        )
    );
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($httpCode, $response);
    } //API JSON请求接口
    public function smssend($tel,$content){
    $statusStr = array(
    "0" => "短信发送成功",
    "-1" => "参数不全",
    "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
    "30" => "密码错误",
    "40" => "账号不存在",
    "41" => "余额不足",
    "42" => "帐户已过期",
    "43" => "IP地址限制",
    "50" => "内容含有敏感词"
    );
    $smsapi = "http://api.smsbao.com/";  //注册地址，请到：	http://www.smsbao.com/reg?r=11601 该链接申请新注册号有优惠
    $user = ""; //短信平台帐号
    $pass = md5(""); //短信平台密码 
    $content=$content;//要发送的短信内容
    $phone = $tel;//要发送短信的手机号码
    $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
    $result =file_get_contents($sendurl) ;
    return $statusStr[$result];
    } //短信发送指定手机及内容
    public function http_post_json_qcoder($url,$data)
    {
    $data=json_encode($data,true);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
        )
    );
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($httpCode, $response);
    } //微信请求小程序二维码特定请求
    public function getqcoder($path){
    $path="/pages/index/index"; //使用时删除，这里是为了让大家知道这个是用于该界面的
    $appid=""; //APPID 
    $secret=""; //APPsecret
    $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
    $token=C::api_post($url);
    $ttken=$token[1]; 
    $tokenz=json_decode($ttken,true);
    $token=$tokenz["access_token"]; //获得token
    $tourl="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$token;
    $data=[
        "access_token"=>$token,
        "path"=>$path,
        "width"=>400  
        ]; 
    $c=C::http_post_json_qcoder($tourl,$data);
    return $c[1];
    }//调用时，请到调用界面无任何html元素直接调用即可 ,头部需用 ：  header("Content-type: image/jpeg");
}
?>
