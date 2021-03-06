<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function parse_xml($xml)
{
    // 用 simplexml_load_string 函数初步解析 XML，返回值为对象，再通过 normalize_xml 函数将对象转成数组
    return normalize_xml(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS));
}

// 将 XML 解析之后的对象转成数组
function normalize_xml($obj)
{
    $result = null;
    if (is_object($obj)) {
        $obj = (array) $obj;
    }
    if (is_array($obj)) {
        foreach ($obj as $key => $value) {
            $res = normalize_xml($value);
            if (('@attributes' === $key) && ($key)) {
                $result = $res;
            } else {
                $result[$key] = $res;
            }
        }
    } else {
        $result = $obj;
    }
    return $result;
}

//用session_key解密小程序的userinfo
function resolveMiniUserInfo($session_key, $encryptedData, $iv)
{
    $appid = env('WECHAT_MINI_PROGRAM_APPID');
    if (strlen($session_key) != 24) {
        throw new Exception('session_key 长度不对');
    }
    $aesKey=base64_decode($session_key);

    if (strlen($iv) != 24) {
        throw new Exception('iv 长度不对');
    }
    $aesIV=base64_decode($iv);

    $aesCipher=base64_decode($encryptedData);

    $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

    $dataObj=json_decode( $result );
    if( $dataObj  == NULL )
    {
        throw new Exception('解密失败，数据为空');
    }
    if( $dataObj->watermark->appid != $appid )
    {
        throw new Exception('解密失败，appid不对');
    }
    return $result;
}


//获取客户端IP
function get_client_ip(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return($ip);
}

//随机返回图片地址
function get_rand_imgurl()
{
    $avatars = [
        'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531882917857&di=9e1197b3ab56544e1d4c56e6523a96bb&imgtype=0&src=http%3A%2F%2Fimg5.duitang.com%2Fuploads%2Fitem%2F201610%2F14%2F20161014213607_5ciFR.jpeg',
        'https://lccdn.phphub.org/uploads/avatars/1_1530614766.png?imageView2/1/w/200/h/200',
        'https://lccdn.phphub.org/uploads/avatars/5350_1481857380.jpg?imageView2/1/w/100/h/100',
        'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531883452426&di=881f70c78b93bd91906bb224fe12b067&imgtype=0&src=http%3A%2F%2Fpic.hanhande.com%2Ffiles%2F140917%2F1285740_102303_5420.gif',
        'https://lccdn.phphub.org/uploads/avatars/20269_1512030996.jpeg?imageView2/1/w/200/h/200',
        'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1468890659,201072083&fm=27&gp=0.jpg',
        'https://lccdn.phphub.org/uploads/avatars/19867_1515925556.png?imageView2/1/w/200/h/200'
    ];

    return $avatars[rand(0, count($avatars)-1)];
}
