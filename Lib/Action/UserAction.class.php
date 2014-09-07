<?php

class UserAction extends Action
{

    public function login_post()
    {
        $username = $this->_post("username");
        $Password = $this->_post("password");

        $user = new Model("user");
        $condition["username"] = $username;
        $data = $user->where($condition)->find();
        $Password = substr(md5($Password), 8, 16);


        //echo $Password;
        if ($data != null) {
            //echo $Password;
            //echo $data["Password"];
            if ($data["Password"] == $Password) {
                $token = md5($id . $password . $username . time());
                $id = $data["id"];
        $usertype=$data["UserType"];
                unset($data);
                $data["token"] = $token;
                $data["id"] = $id;
        $data["usertype"]=$usertype;
                $user->save($data);
                $user->where("id=$id")->setInc('Logincount');
                $this->response($data, "json", "200");
                exit();
            }
        }

        $this->response("false", "json", "404");
    }

    public function auth()
    {
        $token = $this->_get("token");
        if ($token != null) {
            $condition["token"] = $this->_post("token");
            $user = new Model("user");
            $data = $user->where($condition)->find();
            if ($data != null && $data != false)
            {
                
                return true;
            }
        }
        return false;
    }

    public function reg_post()
    {
        $data = $_POST;
        $data["Password"] = substr(md5($data["Password"]), 8, 16);
        $M = new Model("user");
        $username["username"]=$data["username"];
        $email["email"]=$data["email"];
        $Dusername = $M->where($username)->find();
        $Demail    = $M->where($email)->find();
        $rdata["status"]=1;//假定成功
        $rdata["username"]=1;
        $rdata["email"]=1;
        $data["token"]="first";
        // $rdata[3]=$Dusername;
        // $rdata[4]=$Demail;
        if($Dusername!=null)
        {
            $rdata["status"]=0;   //注册不成功
            $rdata["username"]=0;
        }
        if($Demail!=null)
        {
            $rdata["status"]=0;
            $rdata["email"]=0;
        }
        if($rdata["status"]==1)
        {
            $rdata["id"]=$M->add($data);
        }
        $this->response($rdata,json,"200");

    }

    public function user_get()
    {


        $condition["id"] = $this->_get("id");
        // if($this->check!=0)
        //     $condition["check"]=$this->check;
        $user = new Model("userview");
        $data = $user->where($condition)->find();
        if(($data["headPiC"]=="")||$data["headPiC"]==null)
        {
            $data["headPiC"]="nopic.jpg";
        }
        if(($data["modelcardurl"]=="")||$data["modelcardurl"]==null)
        {
            $data["modelcardurl"]="nopic.jpg";
        }
        else
        {
            $data["Ismodelcardshow"]=0;
        }
        $this->response($data, json, "200");
    }

    public function userbak_post()
    {
        $data = $this->_post();
        $user = new Model("user");
        $data["regTime"] = date("Y/m/d h:m:s");
        $res = $user->add($data);
        if ($res != null) {
            $this->response($res);
            exit();
        }
        $this->response("false", "json", "401");
    }

    public function user_post()
    {

        $data = $_POST;
        $id = $data["id"];
        $M = new Model("user");
        //$id=$data["id"];
        $M->where("id=$id")->save($data);
    }

    public function photolist_get()
    {
        $condition["userid"] = $this->_get("id");
        $num = $this->_get("num");
        if($this->_get("style")!=null)
            $condition["ProClass"] = $this->_get("style");
        if($this->_get("page")!=null)
            $page=$this->_get("page");
        else
            $page = 1;
   //     var_dump($condition);
        unset($data);
        $M = new Model("photo");
        $data = $M->where($condition)->page($page,$num)->select();
       // var_dump($data);
        $this->response($data, 'json');
    }
    public function photo_post()
    {
        $data = $_POST;
        $id = $data["Id"];
        $M = new Model("photo");
        //$id=$data["id"];
        if($data["action"]!="delete")
        {
            unset($data["action"]);
            if ($id == null) {
                $M->add($data);
            } else {
                $M->where("Id=$id")->save($data);
            }
        }
        else
        {
            $M->where("Id=$id")->delete();
        }

    }
    function thumb($width,$uploadfile)
    {

       $dstW=$width;//缩略图宽
       $smallfile=$uploadfile.'_'.$width."x999.jpg";

       $src_image=ImageCreateFromJPEG($uploadfile);
       $srcW=ImageSX($src_image); //获得图片宽
       $srcH=ImageSY($src_image); //获得图片高
       $radio = $dstW/$srcW;
       $dstH = $srcH*$radio;
       $dst_image=ImageCreateTrueColor($dstW,$dstH);
       ImageCopyResized($dst_image,$src_image,0,0,0,0,$dstW,$dstH,$srcW,$srcH);
       ImageJpeg($dst_image,$smallfile);

    }
    public function image_post()
    {
        $path = $this->_post("type");
        import('ORG.Net.UploadFile');
        import('ORG.Util.Image');
        $upload = new UploadFile(); // 实例化上传类
        $upload->maxSize = 30145728; // 设置附件上传大小
        $filename = date("YmdHis") . uniqid();
        //$file_ext=  $this->_post("file_ext");
        $upload->saveRule = $filename;
        $upload->$allowExts = array(
            'gif',
            'jpg',
            'jpeg',
            'png',
            'bmp',
            'flv',
            'mp4'); 
        $upload->savePath = './' . $path . '/'; // 设置附件上传目录

        if (!$upload->upload()) { // 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        } else { // 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
        }
        
        $data["status"] = 1;
        $data["filename"] = $info[0]["savename"];
        if($path=="photo")
        {
            $uploadfile=$upload->savePath.$data["filename"];
            $this->thumb(250,$uploadfile);
        }
        $this->response($data, 'json', 200);
        // 保存表单数据 包括附件数据
        //        $User = M("User"); // 实例化User对象
        //        $User->create(); // 创建数据对象
        //        $User->photo = $info[0]['savename']; // 保存上传的照片根据需要自行组装
        //        $User->add(); // 写入用户数据到数据库
        //        $this->success('数据保存成功！');
    }

    public function photo_get()
    {
        $M = new Model("photo");
      
        $condition["Id"] = $this->_get("Id");
        $data = $M->where($condition)->select();
        $this->response($data, 'json');
    }

    public function style_get()
    {
        $id = $this->_get("id");

        $M = new Model("user");
        $data = $M->where("id=$id")->field("styles")->find();
        $style = $data["styles"];
        $style = explode(',', $style);
        $M = new Model("style");
        $ClassName = $M->getField("Id,ClassName");

        $k = 0;
        for ($i = 0; $i < count($style); $i++) {

            if ($style[$i] != null) {
                $j = $style[$i];
                $slist[$k]["Id"] = $j;
                $slist[$k]["ClassName"] = $ClassName[$j];
                $k++;
            }
        }
        $this->response($slist, 'json');
    }

    public function product_get()
    {
        $id = $this->_get("id");
        //echo $id;
        $type = $this->_get("type");
        if ($type != null) {
            $user = new Model("order");
        } else {
            $user = new Model("userview");
        }
        $M = new Model("producttype");

        $product = $user->where("id=$id")->field("ProductTypes")->find();
        $product = explode(',', $product["ProductTypes"]);
        $ClassName = $M->getField("Id,ClassName");

        //$this->response($product);
        $k = 0;
        for ($i = 0; $i < count($product); $i++) {
            if ($product[$i] != null) {
                $j = $product[$i];
                $plist[$k]["Id"] = $j;
                $plist[$k]["ClassName"] = $ClassName[$j];
                $k++;
            }
            //$res.=$pro[$i]." ";
        }
        $this->response($plist, 'json', '200');
    }

    public function order_get()
    {
        $id = $this->_get("id");
        $M = new Model("order");
        $D = $M->where("modelid=$id")->select();
        $M = new Model("producttype");
        $ClassName = $M->select();
        for ($i = 0; $i < count($D); $i++) {
            $product = explode(',', $D[$i]["ProductTypes"]);
            for ($k = 0; $k < count($product); $k++) {
                if ($product[$k] != null) {
                    $j = $product[$k];
                    $pro .= $ClassName[$j]["ClassName"] . " ";
                }
            }
            $D[$i]["ProductTypes"] = $pro;
            //$res.=$pro[$i]." ";
        }
        $this->response($D, 'json');
    }

    public function order_post()
    {
        $data = $_POST;
        $M = new Model("order");
        $data["Addtime"] = time("Yms hms");
        $M->add($data);
    }

    public function search_get()
    {
        $condition = $this->_get("condition");
        $condition = urldecode($condition);
        $condition = "select * from user where " . $condition;
        //echo $condition;
        $M = new Model();
        $D = $M->query($condition);
        $this->response($D, 'json');
    }
    public function crop_post()
    {
        //echo "crop";
        $targ_w = 250;
        $targ_h = 394;
        $jpeg_quality = 90;
        var_dump($_POST);
        if ($_POST["inputimgurl"] != null)
            $src = $_POST["inputimgurl"];
        else
            exit;

        $id = $_POST["id"];
        if ($id == null)
            exit;
        $img_r = imagecreatefromjpeg("./Temp/$src");
        $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
        $arr = getimagesize("./Temp/$src");
        $rate = $arr[0] / 400;
        echo $rate;
        imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'] * $rate, $_POST['y'] * $rate,
            $targ_w, $targ_h, $_POST['w'] * $rate, $_POST['h'] * $rate);

        //header('Content-type: image/jpeg');
        imagejpeg($dst_r, "./photo/$src", $jpeg_quality);
        $M = new Model("user");
        $data["headPiC"]=$src;
    //    var_dump($data);
        $M->where("id=$id")->save($data);
       // echo "操作成功";
       // sleep(3);
       // header("Location: /mm/User/headpic");
        exit;
    }
    public function test_post()
    {
        var_dump($_POST);
    }

}

?>
