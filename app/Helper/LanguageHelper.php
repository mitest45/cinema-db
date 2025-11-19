<?php 

if(!function_exists('_l')){
    function _l($str){
        return ucfirst(str_replace('_',' ',$str));
    }
}
?>