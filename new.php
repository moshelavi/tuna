<?php
// קובץ טיונר טלפוני לדוגמה עבור מערכת ימות המשיח
define("URL", "https://www.call2all.co.il/ym/api/");
$server = "www";
// הגדרת כותרות התגובה כקובץ XML
header('Content-Type: text/xml; charset=UTF-8');

class BodyPost
{
// part "multipart/form-data"
    public static function PartPost($name, $val)
     {
         $body = 'Content-Disposition: form-data; name="' . $name . '"';
// check instance of oFile
         if($val instanceof oFile)
          {
              $file = $val->Name();
              $mime = $val->Mime();
              $cont = $val->Content();
 
              $body .= '; filename="' . $file . '"' . "\r\n";
              $body .= 'Content-Type: ' . $mime ."\r\n\r\n";
              $body .= $cont."\r\n";
          } else $body .= "\r\n\r\n".$val."\r\n";
         return $body;
     }
 
    public static function Get(array $post, $delimiter = '-------------0123456789')
     {
         if(is_array($post) && !empty($post))
          {
              $bool = true;
              //foreach($post as $val) if($val instanceof oFile) {$bool = true; break; };
              if($bool)
               {
                   $ret = '';
                   foreach($post as $name=>$val)
                       $ret .= '--' . $delimiter. "\r\n". self::PartPost($name, $val);
                   $ret .= "--" . $delimiter . "--\r\n";
               } else $ret = http_build_query($post);
          } else throw new \Exception('Error input param!');
         return $ret;
     }
}
 
class oFile
{
     private $name;
     private $mime;
     private $content;
 
     public function __construct($name, $mime=null, $content=null)
      {
          if(is_null($content))
           {
               $info = pathinfo($name);
// check is exist and readable file
               if(!empty($info['basename']) && is_readable($name))
                {
                    $this->name = $info['basename'];
// get MIME
                    $this->mime = mime_content_type($name);
// load file
                    $content = file_get_contents($name);
                    if($content!==false)
                    {
                        $this->content = $content;
                    }
                    else
                    {
                        throw new Exception('Don`t get content - "'.$name.'"');
                    }
                }
                else
                {
                    throw new Exception('Error param');
                }
           }
           else
           {
                   $this->name = $name;
                   if(is_null($mime)) $mime = mime_content_type($name);
                   $this->mime = $mime;
                   $this->content = $content;
            };
      }
 
    public function Name() { return $this->name; }
 
    public function Mime() { return $this->mime; }
 
    public function Content() { return $this->content; }
 
}
 
class connecting_to_yemot_api
{
    public $token;
 
    const URL = URL;
 
    public function __construct($user_name, $password)
    {
        $body = array('username' => $user_name, 'password' => $password);
 
        $body = http_build_query($body);
 
        $opts = array('http' => array(
 
            'method'  => 'POST',
 
            'header'  => "Content-Type: application/x-www-form-urlencoded",
 
            'content' => $body,
 
            'follow_location' => false) );
 
         $context  = stream_context_create($opts);
 
         $url = self::URL.'Login';
 
            $result = file_get_contents($url, FALSE, $context);
 
            $result = json_decode($result);
 
            if($result -> responseStatus == 'OK')
            {
                $this -> token = $result -> token;
 
                return TRUE;
            }
            else
            {
                throw new Exception('שם המשתמש או הסיסמא של המערכת שגויים');
            }
    }
 
    public function __destruct()
    {
        $this -> connecting('Logout');
    }
 
    public function connecting($action, $body = array())
    {
        $delimiter = '----'.uniqid();
 
        $body['token'] = $this -> token;
 
        $body = BodyPost::Get($body, $delimiter);
 
        $opts = array('http' =>
                array(
                    'method'  => 'POST',
                    'header'  => 'Content-Type: multipart/form-data; boundary='.$delimiter,
                    'content' => $body,
                    'follow_location' => false
                    )
                  );
        $context  = stream_context_create($opts);
 
        $url = self::URL.$action;
 
        $result = file_get_contents($url, FALSE, $context);
 
        $headers = $this -> parseHeaders($http_response_header);
 
        if($headers['Content-Type'][0] == 'application/json')
        {
            return json_decode($result);
        }
        else
        {
            return $result;
        }
    }
 
    private function parseHeaders($headers)
    {
        // פונקציה שמקבלת מערך של שורות הכותרות
        // הפונקציה מפרקת את קבצי הקוקי לתת-מערך נפרד
 
 
        // מערך הכותרות
        $head = array();
 
        foreach( $headers as $k=>$v )
        {
            $t = explode( ':', $v, 2 );
 
            if( isset( $t[1] ) )
            {
                if($t[0] == 'Set-Cookie')
                {
                    $CookiesArr = array();
 
                    $cookies = explode( ';', $t[1]);
 
                    foreach($cookies as $cookie)
                    {
                        $c = explode( '=', $cookie);
 
                        if( isset( $c[1] ) )
                        {
                            $CookiesArr[ trim($c[0]) ] = trim( $c[1] );
                        }
                        else
                        {
                            $CookiesArr[] = trim( $c[0] );
                        }
                    }
 
                    $head[ trim($t[0]) ] = $CookiesArr;
                }
                elseif($t[0] == 'Content-Type')
                {
                    $arr = array();
 
                    $children = explode( ';', $t[1]);
 
                    foreach($children as $child)
                    {
                        $c = explode( '=', $child);
 
                        if( isset( $c[1] ) )
                        {
                            $arr[ trim($c[0]) ] = trim( $c[1] );
                        }
                        else
                        {
                            $arr[] = trim( $c[0] );
                        }
                    }
 
                    $head[ trim($t[0]) ] = $arr;
                }
                else
                {
                    $head[ trim($t[0]) ] = trim( $t[1] );
                }
            }
            else
            {
                $head[] = $v;
                if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                {
                    $head['reponse_code'] = intval($out[1]);
                }
            }
        }
        return $head;
    }
}
$con = new connecting_to_yemot_api('0799418110','3356');
$token = "0799418110:3356";
// הפונקציה שמחזירה את ה-XML למערכת ימות המשיח
function respond($response) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<Response>';
    echo $response;
    echo '</Response>';
}

// בדיקה אם התקבל קלט כלשהו מהמקש שהמשתמש הקיש
if (isset($_GET['Digits'])) {
    $digits = $_GET['Digits']; // קבלת המקש שהוקש

    // בניית התגובה בהתאם למקש שהמשתמש לחץ
    switch ($digits) {
        case '1':
            respond('<Say>לחצת על המקש 1. תודה!</Say>');
            break;
        case '2':
            respond('<Say>לחצת על המקש 2. כל הכבוד!</Say>');
            break;
        case '3':
            respond('<Say>לחצת על המקש 3. יופי!</Say>');
            break;
        default:
            respond('<Say>הבחירה שלך לא ידועה. נסה שוב.</Say>');
            break;
    }
} else {
    // הודעה במקרה שלא הוקש מקש
    respond('<Gather numDigits="1">
                <Say>נא ללחוץ על מקש 1 עד 3 כדי לבחור.</Say>
            </Gather>');
}
?>
