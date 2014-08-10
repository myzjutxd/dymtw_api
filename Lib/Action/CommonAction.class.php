<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class CommonAction extends Action
{
    public function product_get() {

		$M = new Model("producttype");
		$product =$this->_get("product");
		$product = explode(',', $product["ProductTypes"]);
		$ClassName = $M -> getField("Id,ClassName");

		//$this->response($product);
                $k=0;
		for ($i = 0; $i < count($product); $i++) {
			if ($product[$i] != null) {
				$j = $product[$i];
                                $plist[$k]["Id"]=$j;
				$plist[$k]["ClassName"] = $ClassName[$j];
                                $k++;
			}
			//$res.=$pro[$i]." ";
		}
		$this -> response($plist, 'json', '200');

	}
}
