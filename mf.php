<?php
class mf {
    private $email;
    private $pass;
    public $session_token;
    public $downloads;
    function __construct($email, $pass, $fileid = false) {
        $this->email = $email;
        $this->pass = $pass;
        if (file_exists('mftoken4xfhs.txt')) {
            $this->session_token = file_get_contents('mftoken4xfhs.txt');
            //use session token saved in file.
           if ($this->session_token === "error") {
               $this->login();
            //login and set cookies.
            $this->session_token = $this->get_session();
            $myfile = fopen("mftoken4xfhs.txt", "w") or die("Unable to open file!");
            fwrite($myfile, $this->session_token);
            fclose($myfile);
           }

        } else {
            $this->login();
            //login and set cookies.
            $this->session_token = $this->get_session();
            $myfile = fopen("mftoken4xfhs.txt", "w") or die("Unable to open file!");
            fwrite($myfile, $this->session_token);
            fclose($myfile);
        }





    }
    private function startt() {

        $ch = curl_init();
        $timeout = 20;
        curl_setopt($ch, CURLOPT_URL, 'https://www.mediafire.com');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);

    }
    private function formatlink($urlid) {
        if (strpos($urlid, 'mediafire.com/file/') !== false) {
            $isi = explode('/', explode('/file/', $urlid)[1])[0];
            return $isi;
        } else {
            return $urlid;
        }
    }
    private function grabsec() {
        //grabs security token for submitting login form.
        $this->startt();
        $ch = curl_init();
        $timeout = 20;
        curl_setopt($ch, CURLOPT_URL, 'https://www.mediafire.com/login/');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        $scriptx = "";
        $internalErrors = libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML($data);
        $xpath = new DOMXPath($dom);
        $nlist = $xpath->query("//input[@name='security']");
        $csrfToken = $nlist[0]->getAttribute('value');

        return $csrfToken;

    }
    public function stream_link($url) {
        $streamfire = "";
        $ch = @curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $head[] = "Connection: keep-alive";
        $head[] = "Keep-Alive: 300";
        $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $head[] = "Accept-Language: en-us,en;q=0.5";
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        $data = curl_exec($ch);
        curl_close($ch);

        $scriptx = "";
        $internalErrors = libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML($data);
        $xpath = new DOMXPath($dom);
        $nlist = $xpath->query("//a");
        foreach ($nlist as $k => $c) {
            if (strpos($nlist[$k]->nodeValue, 'Download') !== false) {
                if (strpos($nlist[$k]->getAttribute('href'), 'javascript:') !== false) {} else {
                    $streamfire = $nlist[$k]->getAttribute('href');
                    $streamfire = str_replace('http:', 'https:', $streamfire);
                    return $streamfire;
                }
            }
        }


    }
    public function copy_file($fileid, $folderid = 'null') {
        $fileid = $this->formatlink($fileid);
        $url = "http://www.mediafire.com/api/file/copy.php";
        $postinfo = "session_token=".$this->session_token."&response_format=json&folder_key=".$folderid."&quick_key=".$fileid;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.mediafire.com/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        $isf = curl_exec($ch);
        $jso = json_decode($isf, true);
        return json_encode($jso, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
    public function delete_file($fileid) {
        $fileid = $this->formatlink($fileid);
        $url = "http://www.mediafire.com/api/1.5/file/delete.php";
        $postinfo = "session_token=".$this->session_token."&response_format=json&quick_key=".$fileid;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.mediafire.com/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        $isf = curl_exec($ch);
        $this->empty_trash();
        $jso = json_decode($isf, true);
        return json_encode($jso, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
    
private function empty_trash() {
     $ch = curl_init();
     $postinfo = "session_token=".$this->session_token."&response_format=json";
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, 'http://www.mediafire.com/api/1.5/device/empty_trash.php');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.mediafire.com/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        $isf = curl_exec($ch);
    
}
    

    public function file_info($fileid) {
        $fileid = $this->formatlink($fileid);
        $url = "http://www.mediafire.com/api/file/get_info.php";
        $postinfo = "session_token=".$this->session_token."&response_format=json&quick_key=".$fileid;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.mediafire.com/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        $isf = curl_exec($ch);
        $jso = json_decode($isf, true);
        if ($jso['response']['result'] === "Error") {
           //  $this->login();
            //login and set cookies.
            $this->session_token = $this->get_session();
            $myfile = fopen("mftoken4xfhs.txt", "w") or die("Unable to open file!");
            fwrite($myfile, $this->session_token);
            fclose($myfile);
            return $this->file_info($fileid);
        }
        if (isset($jso['response']['file_info']['downloads'])) {
            $this->downloads = $jso['response']['file_info']['downloads'];
        } else {
            $this->downloads = 'null';
        }

        return json_encode($jso, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
    public function search_folder($search, $folderkey = "myfiles") {

        $url = "http://www.mediafire.com/api/folder/search.php";
        $postinfo = "session_token=".$this->session_token."&response_format=json&folder_key=".$folderkey."&search_text=".$search."&details=yes";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.mediafire.com/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        $isf = curl_exec($ch);
        $jso = json_decode($isf, true);
        foreach ($jso['response']['results'] as $k => $file) {
            $jso['response']['results'][$k]['full'] = 'https://www.mediafire.com/file/'.$file['quickkey'].'/file';
        }
        return json_encode($jso, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

    }
    public function get_session() {

        $url = "https://www.mediafire.com/application/get_session_token.php";
        //get api session_token for making calls later
        $postinfo = "session_token=&response_format=json";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.mediafire.com/');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        $isf = curl_exec($ch);
        $jsig = json_decode($isf, true);
        if (isset($jsig['response']['error']) && strlen($jsig['response']['error']) > 0) {
            return "error";
        } else {
            return $jsig['response']['session_token'];
        }


    }
    public function get_content($folderid = 'myfiles') {

    $url="https://www.mediafire.com/api/1.5/folder/get_content.php"; 
$postinfo = "session_token=".$this->session_token."&response_format=json&folder_key=".$folderid."&content_type=files&chunk=1&chunk_size=200&details=yes&order_direction=asc&order_by=name&filter=";
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookifemf.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookifemf.txt');
curl_setopt($ch, CURLOPT_USERAGENT,
    "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_REFERER, 'https://www.mediafire.com/');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
$isf = curl_exec($ch);
$jso = json_decode($isf, true);
return json_encode($jso, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}
    public function login() {
        //login to mediafire and set cookies.
        $security = $this->grabsec();
        //grab csrf token for login post.
        $url = "https://www.mediafire.com/dynamic/client_login/mediafire.php";
        //https://www.mediafire.com/login/
        $postinfo = "security=".$security."&login_email=".$this->email."&login_pass=".$this->pass."&login_remember=on";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiemf.txt');
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:80.0) Gecko/20100101 Firefox/80.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.mediafire.com/login/');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        $isf = curl_exec($ch);

    }
}



?>
