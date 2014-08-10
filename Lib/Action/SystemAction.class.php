<?php
class SystemAction extends Action
{
    public function dev()
    {
        $dev = new Model("dev");
        if($_POST!=null)
        {
            $data=$_POST;
            //var_dump($data);
            $dev->save($data);
            
            echo "success";
			exit();
            
        }
        
        
            $info=$dev->select();
            $this->assign("info",$info);
            $this->display();
        
        
    }
    public function createToken()
    {
        $user = new Model("Dev");
        $id=$this->_get("id");
        $username=$this->_get("username");
        $token=md5($id.base64_encode($username).time());
        echo $token;
    }

}