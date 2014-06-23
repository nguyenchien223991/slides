<?php
 //if(!define('NV_MAINFILE')) die('Stop !!! ');
 if(!function_exists('nv_block_slider')){
        
     function nv_block_slider(){
        $sql="SELECT * FROM `nv4_vi_slides_picture` WHERE 1";
        //$q=$db->query($sql);

        
        $list=nv_db_cache($sql);
        foreach($list as $l){
            
            //code tiếp
            
           //$xtpl->assign('LOOP', $l);
          // $xtpl->prase('main.loop');
        }
        
         print_r($list); die();
    
        
        
        $xtpl = new XTemplate('block_slider.tpl', NV_ROOTDIR .'/themes/default/blocks');
        $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
        $xtpl->parse('main');
        return $xtpl->text('main');
     }
 $contents = nv_block_slider();
    
 }
 


?>