<?php
namespace common\tool;

/**
 * Created by PhpStorm.
 */
class Fun extends \yii\base\Object
{


    public static function formatNumberToPrice($number_price = 0)
    {
        $number_price = $number_price ? $number_price : 0;
        return number_format($number_price, 2, '.', '');
    }

    /**
     * 获取地址对应的坐标
     * @param $address
     * @return array
     */
    public static function addressPoint($address){
        $lng = 0;
        $lat = 0;
        //$data = file_get_contents('http://api.map.baidu.com/geocoder?output=json&address='.urlencode($address));
		$data = self::getCurl('http://api.map.baidu.com/geocoder?output=json&address='.urlencode($address),array(),'get');
        $data = json_decode($data,true);
        if($data && $data['status'] == 'OK' && isset($data['result']) && isset($data['result']['location']))
        {
            $lng = $data['result']['location']['lng'];
            $lat = $data['result']['location']['lat'];
        }
        return array($lng,$lat);
    }

	/**
	 * 获取客户端ip地址
	*/
	public static function getClientIp()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else if (isset($_SERVER['REMOTE_ADDR']))
		{
			return $_SERVER['REMOTE_ADDR'];
		}
		return '0.0.0';
	}

	/**
	 *  可逆加密解密  leo
	 * @example
	 *
	 *  $a = 'yanzhiwei';
	 *   // 加密
	 *  $b = G4S::extendEncrypt($a,'www');
	 *   // 解密
	 *  echo G4S::extendDecrypt($b,'www');die;
	 *
	 *
	 **/
	// 解密
	static function extendDecrypt($encryptedText,$key)
	{
		$cryptText 		= base64_decode($encryptedText);
		$ivSize 		= mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv 			= mcrypt_create_iv($ivSize, MCRYPT_RAND);
		$decryptText 	= mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptText, MCRYPT_MODE_ECB, $iv);

		return trim($decryptText);
	}

	//可逆加密
	static function extendEncrypt($plainText,$key)
	{
		$ivSize			= mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv 			= mcrypt_create_iv($ivSize, MCRYPT_RAND);
		$encryptText	= mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plainText, MCRYPT_MODE_ECB, $iv);

		return trim(base64_encode($encryptText));

	}


	/**
	 * 生成缩略图
	 * @param string     源图绝对完整地址{带文件名及后缀名}
	 * @param string     目标图绝对完整地址{带文件名及后缀名}
	 * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
	 * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
	 * @param int        是否裁切{宽,高必须非0}
	 * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
	 * @return boolean
	 */
	public static function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
	{
		if(!is_file($src_img))
		{
			return false;
		}
		$ot = pathinfo($dst_img, PATHINFO_EXTENSION);

		$otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
		$srcinfo = getimagesize($src_img);
		$src_w = $srcinfo[0];
		$src_h = $srcinfo[1];
		$type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
		$createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);

		if(!$width){
			$width = $height*$src_w/$src_h;
		}
		if(!$height){
			$height = $width*$src_h/$src_w;
		}

		$dst_h = $height;
		$dst_w = $width;
		$x = $y = 0;
		/**
		 * 缩略图不超过源图尺寸（前提是宽或高只有一个）
		 */
		if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
		{
			$proportion = 1;
		}
		if($width> $src_w)
		{
			$dst_w = $width = $src_w;
		}
		if($height> $src_h)
		{
			$dst_h = $height = $src_h;
		}

		if(!$width && !$height && !$proportion)
		{
			return false;
		}
		if(!$proportion)
		{
			if($cut == 0)
			{
				if($dst_w && $dst_h)
				{
					if($dst_w/$src_w> $dst_h/$src_h)
					{
						$dst_w = $src_w * ($dst_h / $src_h);
						$x = 0 - ($dst_w - $width) / 2;
					}
					else
					{
						$dst_h = $src_h * ($dst_w / $src_w);
						$y = 0 - ($dst_h - $height) / 2;
					}
				}
				else if($dst_w xor $dst_h)
				{
					if($dst_w && !$dst_h)  //有宽无高
					{
						$propor = $dst_w / $src_w;
						$height = $dst_h  = $src_h * $propor;
					}
					else if(!$dst_w && $dst_h)  //有高无宽
					{
						$propor = $dst_h / $src_h;
						$width  = $dst_w = $src_w * $propor;
					}
				}
			}
			else
			{
				if(!$dst_h)  //裁剪时无高
				{
					$height = $dst_h = $dst_w;
				}
				if(!$dst_w)  //裁剪时无宽
				{
					$width = $dst_w = $dst_h;
				}
				$propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
				$dst_w = (int)round($src_w * $propor);
				$dst_h = (int)round($src_h * $propor);
				$x = ($width - $dst_w) / 2;
				$y = ($height - $dst_h) / 2;
			}
		}
		else
		{
			$proportion = min($proportion, 1);
			$height = $dst_h = $src_h * $proportion;
			$width  = $dst_w = $src_w * $proportion;
		}

		$src = $createfun($src_img);
		$dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
		$white = imagecolorallocate($dst, 255, 255, 255);
		imagefill($dst, 0, 0, $white);

		if(function_exists('imagecopyresampled'))
		{
			imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		}
		else
		{
			imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		}
		imagejpeg($dst, $dst_img);
		imagedestroy($dst);
		imagedestroy($src);
		return true;
	}

	static function getCurl($url, array $vars = array(), $method = 'post')
	{
		$method = strtolower($method);
		if ($method == 'get' && !empty($vars)) {
			if (strpos($url, '?') === false)
				$url = $url . '?' . http_build_query($vars);
			else
				$url = $url . '&' . http_build_query($vars);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if ($method == 'post') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		}
		$result = curl_exec($ch);
		if (!curl_errno($ch)) {
			$result = trim($result);
		} else {
			$result = '';
		}

		curl_close($ch);
		return $result;

	}



}