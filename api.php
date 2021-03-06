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
    public function toexcel($arr=[0=>[0=>'value',],],$excelname); //Excel导出
    public function upfile($cfile); //文件上传
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
         if($qEof->num_rows>0){ //优化有值的情况下再进行数据你懂的
        $i=0;
        while($qq=$qEof->fetch_assoc()){
            $q[$i]=$qq;
            $i++;
        }
        return $q;
         }
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
            //    $cb.="'".$cvalue[$j]."'";
                if($cvalue[$j]==NULL){
                   $cb.="NULL";
                }else{
                $cb.="'".$cvalue[$j]."'";
                }
            }else{
             //   $cb.="'".$cvalue[$j]."',";
              if($cvalue[$j]==NULL){
                   $cb.="NULL,";
                }else{
                $cb.="'".$cvalue[$j]."',";
                }
            }
        }
        $isql="INSERT INTO `$tablename` ($ct) VALUES ($cb)";
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
            $i++;
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


    public function toexcel($arr=[0=>[0=>'value',],],$excelname){
    // $arr为无组名数组
    //输出的文件类型为excel  
    header("Content-type:application/vnd.ms-excel");  
    //提示下载  
    header("Content-Disposition:attachement;filename=".$excelname.".xls");  
    //报表数据  
    $ReportArr = $arr;  
    $ReportContent = '';  
    $num1 = count($ReportArr);  
    for($i=0;$i<$num1;$i++){  
        $num2 = count($ReportArr[$i]);  
        for($j=0;$j<$num2;$j++){  
            //ecxel都是一格一格的，用\t将每一行的数据连接起来  
            $ReportContent .= '"'.(string)$ReportArr[$i][$j].'"'."\t";  
        }  
        //最后连接\n 表示换行  
        $ReportContent .= "\n";  
    }  
    //用的utf-8 最后转换一个编码为gb  
    // $ReportContent = mb_convert_encoding($ReportContent,"gb2312","utf-8");  
    //输出即提示下载  
    echo $ReportContent;  
    }
    public function upfile($cfile){ //$cfile为前端post时的表单名称 以及form需增加enctype="multipart/form-data" 以及不同环境需要修改其上传路径
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES[$cfile]["name"]);
    $extension = end($temp);     // 获取文件后缀名
    if ((($_FILES[$cfile]["type"] == "image/gif")
|| ($_FILES[$cfile]["type"] == "image/jpeg")
|| ($_FILES[$cfile]["type"] == "image/jpg")
|| ($_FILES[$cfile]["type"] == "image/pjpeg")
|| ($_FILES[$cfile]["type"] == "image/x-png")
|| ($_FILES[$cfile]["type"] == "image/png"))
&& ($_FILES[$cfile]["size"] < 204800)   // 小于 200 kb
&& in_array($extension, $allowedExts))
{
     echo "aaa";
    if ($_FILES[$cfile]["error"] > 0)
    {
         
    }
    else
    {
        if (file_exists("/cocoapp/file/" . $_FILES[$cfile]["name"]))
        {
            echo $_FILES[$cfile]["name"] . " 文件已经存在。 ";
        }
        else
        {
            $cocofilein=time().rand(999,10000).".".str_replace("image/", "",$_FILES[$cfile]["type"]);
             move_uploaded_file($_FILES[$cfile]["tmp_name"], "D:/wwwroot/temp/cocoapp/file/" . $cocofilein);
            $cocofile= "/cocoapp/file/".$cocofilein;
        }
    }
    return  $cocofile;
}
    }
    
}
 

?>
