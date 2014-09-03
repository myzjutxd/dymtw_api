<?php

class IndexAction extends Action

{
public $area;
public $price;
public $check;
    public function _initialize() {

     //   $condition["username"]=$_SERVER["PHP_AUTH_USER"];
        $condition["token"]=$this->_get("token");
        //$password=$_SERVER["PHP_AUTH_PW"];
       // var_dump($_SERVER);
        $M=new Model("dev");
        $data=$M->where($condition)->find();
        //var_dump($data);
        if($data)
        {
            $this->area=(int)$data["area"];
            $this->price=$data["price"]+1;
            if($data["check"]==1)
                $this->check=1;
            

         //   echo $this->area;
        }
        else
        {
            $this->response("errer","json",'404');
        }

    }

    public function list_get()

    {

        $num = $this->_get("num");

        $page=$this->_get("page");
        $type=$this->_get("type");
        if($page==null)
            $page=1;
     //   else
           // $page=$page-1;

        $modeltype=$this->_get("modeltype");

        $istop = $this->_get("istop");

        

        if($modeltype!=null)

            $condition["ModelType"]=$modeltype;

        if($istop!=null)
            $condition["IsTop"]=$istop;
        $condition["IsShow"]=1;
        $condition["UserType"]=1;
        if($this->area!=0)
            
        {
            if($this->area>=5)
                $condition["cityId"]=$this->area;
            else
                $condition["proid"]=$this->area;
        }
        if($this->check==1)
            $condition["check"]=1;
            
      // var_dump($condition);
     //   echo $this->area;
        if($type=="model")
            $condition["Ismodelcardshow"]=1;
        $M = new Model("userview");

        $data{"count"}= $M->where($condition)->count();
        $data["user"] = $M->where($condition)->page($page,$num)->order("LoginTime desc")->select();
        
    
        for($i=0;$i<count($data["user"]);$i++)
        {
            $id = $data["user"][$i]['id'];
           // echo $id;
            $data["user"][$i]["style"] = $this->style($id);
            //$data["user"][$i]["product"]=$this->product_get($id);
            $data["user"][$i]["NeiUnitPrice"]=round($data["user"][$i]["NeiUnitPrice"]*$this->price/10)*10;
            $data["user"][$i]["WaiUnitPrice"]=round($data["user"][$i]["WaiUnitPrice"]*$this->price/10)*10;
            $data["user"][$i]["NeiyiUnitPrice"]=round($data["user"][$i]["NeiyiUnitPrice"]*$this->price/10)*10;
            $data["user"][$i]["DayPrice"]=round($data["user"][$i]["DayPrice"]*$this->price/10)*10;
        }
        //field("id,headPiC,NickName,height,viewcount")

        $this->response($data,'json');

    }
    public function modeltype_get()
    {
        $M = new Model("modeltype");
        $data = $M->where("IsShow=1")->select();
        $this->response($data,'json');
    }
    public function tuijian_get()

    {

        $modeltype=$this->_get("modeltype");

        $M = new Model("user");
        if($this->area!=0)
            
        {
            if($this->area>=5)
                $condition["cityId"]=$this->area;
            else
                $condition["proid"]=$this->area;
        }
        if($this->check==1)
            $condition["check"]=1;
        $condition["ModelType"]=$modeltype;
        $condition["IStuijian"]=1;
        $data = $M->field("id,headPiC,NickName,height")->where($condition)->limit(5)->select();

        $this->response($data,'json');

    }

    public function piclist_get()

    {

        $num = $this->_get("num");
        if($this->_get("id")!=null)
                $condition["userid"]=$this->_get("id");
        if($this->_get("ProClass")!=null)
                $condition["ProClass"]=$this->_get("style");
        if($this->area!=0)
            
        {
            if($this->area>=5)
                $condition["cityId"]=$this->area;
            else
                $condition["proid"]=$this->area;
        }
    if($this->check==1)
         $condition["check"]=1;
        if($num==null)
            $num=10;
        $M = new Model("photoview");

        $data = $M->field("userid,Pic1")->where($condition)->limit($num)->order("UpdateTime desc")->group("userid")->select();

        $this->response($data,'json');

    }

    public function modeltypelist_get()

    {

        $M = new Model("modeltype");

        $data=$M->where("IsShow=1")->select();

        $this->response($data,'json','200');

    }

        public function styleslist_get()

        {

            $M=new Model("style");

            $data=$M->where("IsShow=1")->select();

            $this->response($data,'json','200');

        }

        public function productlist_get()

        {

            $M=new Model("producttype");

            $data=$M->where("IsShow=1")->select();

            $this->response($data,'json','200');

        }

        public function search_get()

        {

            $sdata=$_GET;

            $page=$sdata["page"];

            if($page==null)

                $page=1;

      

            $style=$sdata["styleid"];

            $proid=$sdata["proid"];

            $sdata["usertype"]=1;

            if($sdata["ModelTypeid"]!=null)

                $condition["ModelType"]=$sdata["ModelTypeid"];

            if($style!=null)

                $condition["styles"]=array("like","%$style%");

            if($proid!=null)

                $condition["ProductTypes"]=array("like","%$proid%");

            $condition["height"]=array("between",array($sdata["heightStart"],$sdata["heightEnd"]));

            $condition["OrderCount"]=array("between",array($sdata["OrderCountStart"],$sdata["OrderCountEnd"]));

            $condition["NeiUnitPrice"]=array("between",array(round($sdata["NPriceStart"]*$this->price/10)*10,round($sdata["NPriceEnd"]*$this->price/10)*10));

            $condition["WaiUnitPrice"]=array("between",array(round($sdata["WPriceStart"]*$this->price/10)*10,round($sdata["WPriceEnd"]*$this->price/10)*10));

            $condition["NeiyiUnitPrice"]=array("between",array(round($sdata["NYPriceStart"]*$this->price/10)*10,round($sdata["NYPriceEnd"]*$this->price/10)*10));

            $condition["DayPrice"]=array("between",array($sdata["DPriceStart"]*$this->price,$sdata["DPriceEnd"]*$this->price));
            $condition["IsShow"]=1;
            $condition["UserType"]=1;
            if($this->area!=0)
            
        {
            if($this->area>=5)
                $condition["cityId"]=$this->area;
            else
                $condition["proid"]=$this->area;
        }
         if($this->check==1)
            $condition["check"]=1;
            $user=new Model("userview");
           
                $orderby=$sdata["ordername"]." ".$sdata["ordertype"].",LoginTime desc";
           
            $data["user"]=$user->where($condition)->page($page,30)->order("$orderby")->select();

            $data["count"]=$user->where($condition)->count();

            $this->response($data,'json','200');

        }
        
    public function user_get()
    {


        $condition["id"] = $this->_get("id");
        $user = new Model("userview");
        $data = $user->where($condition)->find();
        $data["NeiUnitPrice"]=round($data["NeiUnitPrice"]*$this->price/10)*10;
        $data["WaiUnitPrice"]=round($data["WaiUnitPrice"]*$this->price/10)*10;
        $data["NeiyiUnitPrice"]=round($data["NeiyiUnitPrice"]*$this->price/10)*10;
        $data["DayPrice"]=round($data["DayPrice"]*$this->price/10)*10;
        $data["style"] = $this->style($condition["id"]);
        $data["product"]=$this->product($condition["id"]);
        if(($data["headPiC"]=="")||$data["headPiC"]==null)
        {
            $data["headPiC"]="nopic.jpg";
        }
        if(($data["modelcardurl"]=="")||$data["modelcardurl"]==null)
        {
            $data["modelcardurl"]="nopic.jpg";
        }
        
        $this->response($data, json, "200");
    }
    public function style($id=null)
    {
        //$id = $this->_get("id");
        if($id==null)
            exit();
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
        return $slist;
    }

    public function product($id=null)
    {
        //$id = $this->_get("id");
        //echo $id;
        if($id==null)
            exit();
        $type = $this->_get("type");
        $user = new Model("userview");
       
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
        return $plist;
    }

    public function orderlist_get()
    {
        $id=$this->_get("id");
        $M = new Model("order");
        $data=$M->where("modelid=$id")->select();
        for($i=0;$i<count($data);$i++)
        {
            $res.=$data[$i]["OrderDay"]     ;
        }
        echo $res;
    }
    public function order_get()
    {
        $id=$this->_get("userid");
        $OrderDay=$this->_get("OrderDay");
        $M = new Model("order");
        $data=$M->where("modelid=$id")->select();
        $this->response($data, json, "200");

    }
    public function user_album()
    {
        $id=$this->_get("id");
        $id;
        $M = new Model("photoview");
        $data=$M->where("userid = $id")->Distinct(true)->field("ProClass")->getField("ProClass,ClassName,Pic1",null);


       // var_dump($data);
        $this->response($data, json, "200");    

    }



}