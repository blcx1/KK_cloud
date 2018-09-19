<?php

namespace Think\Crypt\Driver;
/**
 * Des 加密实现类
 * Converted from JavaScript to PHP by Jim Gibbs, June 2004 Paul Tero, July 2001
 * Optimised for performance with large blocks by Michael Hayworth, November 2001
 * http://www.netdealing.com
 */

class Des2 {

    /**
     * 加密字符串
     * @param string $str 字符串
     * @param string $key 加密key
     * @param integer $expire 有效期（秒）     
     * @return string
     */
    public static function encrypt($str, $key,$expire=0) {
		
		$size = mcrypt_get_block_size('des', 'ecb');          
        $input = self::pkcs5_pad($str, $size);      
        $td = mcrypt_module_open('des', '', 'ecb', '');       
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);      
        @mcrypt_generic_init($td, $key, $iv);         
        $data = mcrypt_generic($td, $input);          
        mcrypt_generic_deinit($td);      
        mcrypt_module_close($td);        
        return $data;     
    }

    /**
     * 解密字符串
     * @param string $str 字符串
     * @param string $key 加密key
     * @return string
     */
    public static function decrypt($str, $key) {
		 
		$encrypted = $str;
        $td = mcrypt_module_open('des','','ecb','');   
        //使用MCRYPT_DES算法,cbc模式                
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);            
        $ks = mcrypt_enc_get_key_size($td);               
        @mcrypt_generic_init($td, $key, $iv);         
        //初始处理                
        $decrypted = @mdecrypt_generic($td, $encrypted);         
        //解密              
        mcrypt_generic_deinit($td);         
        //结束            
        mcrypt_module_close($td);                 
        $y = self::pkcs5_unpad($decrypted);          
        return $y;
    }

    private static function pkcs5_pad($text, $blocksize){
	
        $pad = $blocksize - (strlen($text) % $blocksize);         
        return $text . str_repeat(chr($pad), $pad);   
    }
	
    private static function pkcs5_unpad($text){
	
        $pad = ord($text{strlen($text)-1});       
        if ($pad > strlen($text))              
            return false;         
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)               
            return false;         
        return substr($text, 0, -1 * $pad);   
    }

}