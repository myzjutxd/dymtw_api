<?php
class FileAction extends Action
{

  public $id;
  public $data;
  public $user;
  public function _initialize() {
    $data=$_POST;
    if($data["token"]==null)
    {
      $mdata["status"]=0;
      $mdata["message"]="token id is null";
      $this->response($mdata,"json",'404');
    }
    if($data["id"]==null)
    {
      $mdata["status"]=0;
      $mdata["message"]="id is null";
      $this->response($mdata,"json",'404');
    }
    $M = new Model("user");
    $condition["id"]=$data["id"];
    $user = $M->where($condition)->find();
    if($user==null||$user==false)
    {
      $mdata["status"]=0;
      $mdata["message"]="the user is not true";
      $this->response($mdata,"json",'404');
    }
    CRYPT_SHA256 == 1;
    $token_ture=crypt($user["token"],date("YmdHi"));
    if($data["token"]!=$token_ture)
    {
      $mdata["status"]=0;
      $mdata["message"]="token wrong";
      $this->response($mdata,"json",'404'); 
    }
    else
    {
      $this->id=$data["id"];
      $this->data=$data;
      $this->user=$user;
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
       ImageJpeg($dst_image,$smallfile,100);

    }
    public function upload_post()
    {
        $path = $this->_post("type");
        import('ORG.Net.UploadFile');
        import('ORG.Util.Image');
         $upload = new UploadFile(); // 实例化上传类
         $upload->maxSize = 30145728; // 设置附件上传大小
         $filename = date("YmdHis") . uniqid();
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
        
    }
    public function upload_get()
    {
      echo "test";
    }

}