<?php
namespace App\Libraries;

use DateTime;
use DB;

/**
* 
*/
class Helpers
{
    protected $accounts_email;
    protected $support_email;
    protected $info_email;

    protected $site_name;

    function __construct()
    {
        $this->accounts_email = 'accounts@site.com';
        $this->support_email = 'support@site.com';
        $this->info_email = 'info@site.com';
        $this->site_name = 'SiteName';
    }

    function generate_session($email, $time)
    {
        $newtoken = $this->hashh($email, $time);
        return  $newtoken;
    }
    
    function time_now()
    {
        $main = date('Y-m-d H:i:s');
        return $main;
    }
    
    /**
     * 
     * @param date Current datetime
     * @return date <b>Yesterday</b>
     */
    function yesterday()
    {
        $time = $this->time_now();
        $date = strtotime($time.' -1 day');
    
        return date('Y-m-d', $date);
    }
    
    /**
     * 
     * @return date <b>Today</b>
     */
    function today()
    {
//        $date = strtotime($time.' -'.$diff.' minute');
    
        return date('Y-m-d');
    }
    
    /**
     * 
     * @param date Current datetime
     * @return date <b>Tomorrow</b>
     */
    function tomorrow()
    {
        $time = $this->time_now();
        $date = strtotime($time.' +1 day');
    
        return date('Y-m-d', $date);
    }

    public function time_interval($date_first, $date_second)
    {

        $diff = (strtotime($date_second) - strtotime($date_first));
        return $diff;
    }
    
    function time_diff($time, $diff)
    {
        $date = strtotime($time.' -'.$diff.' year');
        
        return date('Y-m-d H:i:s', $date);
    }
    
    function minute_diff($time, $diff)
    {
        $date = strtotime($time.' -'.$diff.' minute');
    
        return date('Y-m-d H:i:s', $date);
    }
    
    function minute_add($time, $diff)
    {
        $date = strtotime($time.' +'.$diff.' minute');
    
        return date('Y-m-d H:i:s', $date);
    }
    
    function second_diff($time, $diff)
    {
        $date = strtotime($time.' -'.$diff.' second');
    
        return date('Y-m-d H:i:s', $date);
    }

    function timeout($time, $diff)
    {
        $date = strtotime($time.' +'.$diff.' second');
    
        return date('Y-m-d H:i:s', $date);
    }
    
    function hashh($em, $date, $length = 32)
    {
        $sto = "AGLRSTabcUVWXYZdefBCDEFghijkmnopqHIJKrstuvwxyz1023MNOPQ456789";
        $fst = 9;
        $sec = 8;
        $str_em = substr(md5($em.$sto), 1, $fst);
        srand((double)microtime()*1000000);
        $a = explode(":", $date);

        $o =  $a[1].$a[2];
        $p = $a[2].$a[1];
        $q = abs($o - $p);
        $q = substr(md5($q), 1, $sec);
        $i = 1;
        $confirm = '' ;
        
        $new_length = $length - ((int)$fst + (int)$sec);
        while ($i <= $new_length) {
            $num = rand() % 33;
            $temp = substr($sto, $num, 1);
            $confirm = $confirm . $temp;
            $i++;
    
        }
        $confirm = $str_em.$q.$confirm;
        return $confirm;
    }
    
    public function validate_time($time)
    {
        try {
            $date = new DateTime($time);
        } catch (Exception $e) {
            // For demonstration purposes only...
//             print_r(DateTime::getLastErrors());
        
            // The real object oriented way to do this is
            // echo $e->getMessage();
            if ($e->getMessage())
            {
                return 'err';
            }
        }
    }
    
    /**
     * 
     * Explode String with multiple delimeters
     * @return date <b>Yesterday</b>
     */
    public function explode_del_multi($string, $delimiters = [',', ':', ';'])
    {
        if ( ! is_array($delimiters)) $delimiters = (array) $delimiters;
    
        if ( ! count($delimiters)) return $string;
    
        // build escaped regex like /(delimiter_1|delimiter_2|delimiter_3)/
        $regex = '/(';
        $regex .= implode('|', array_map(function ($delimiter) {
            return preg_quote($delimiter);
        }, $delimiters));
            $regex .= ')/';
    
            return preg_split($regex, $string);
    }
    
    public function replace_multi($string, $find, $replace)
    {
//         $find = array(",","---");
//         $replace = array("");
//         $arr = 'some,thing---to:xplode444asd';
        $replaced = str_replace($find,$replace,$string);
        return $replaced;
    }
    
    public function process_mobile($mobile, $countryCode = "234")
    {
        $new_num = "";
        $len_fone = strlen($mobile);
        if ($len_fone == 13 || $len_fone == 11)
        {
            if ($len_fone == 13)
            {
                $cclen = strlen($countryCode);
                $numcode = substr($mobile, 0, $cclen);
                $number = substr($mobile, $cclen);
                $num = $number;
        
                if (strcmp($numcode, $countryCode) == 0 && strlen($number) == 10 && ctype_digit($number))
                {
                    $new_num = $countryCode.$number;
                }
                else
                {
                    $new_num = "";
                }
            }
            if ($len_fone == 11)
            {
                $sub = $mobile[0];
                $number = substr($mobile, 1);
                if ($sub == "0" && strlen($number) == 10 && ctype_digit($number))
                {
                    $new_num = $countryCode.$number;
                }
                else
                {
                    $new_num = "";
                }
            }
        }
        //             consider
        else
        {
            $new_num = "";
        }
        return $new_num;
    }

    public function process_email($email)
    {
        $new_email = $email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $new_email = "";
        }
        return $new_email;
    }

    public function process_username($username)
    {
        $new_username = "";
        $valid = ['-', '_'];
        // if (ctype_space($username))
        // {
        //     $new_username = "";
        // }
        if (ctype_alnum(str_replace($valid, '', $username)))
        {
            $new_username = $username;
        }
        return $new_username;
    }
    
    function validate_date($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    function format_date($date, $format = 'Y-m-d')
    {
        // $d = DateTime::createFromFormat($format, $date);
        $date = date_create($date);
        return date_format($date, $format);
    }

    function format_timestamp($date, $format = 'Y-m-d H:i:s')
    {
        // $d = DateTime::createFromFormat($format, $date);
        $date = date_create($date);
        return date_format($date, $format);
    }
    
    function fullname_decouple($fullname)
    {
        $fn = '';
        $ln = '';
        $mn = '';

        $split = explode(" ", $fullname);
        $name_count = count($split);
        if (count($split) == 3)
        {
            $fn = ucfirst($split[0]);
            $mn = ucfirst($split[1]);
            $ln = ucfirst($split[2]);
        }
        else if (count($split) == 2)
        {
            $fn = ucfirst($split[0]);
            $mn = '';
            $ln = ucfirst($split[1]);
        }
        $name = [
            'first_name' => $fn, 'last_name' => $ln, 'middle_name' => $mn
        ];

        return $name;
    }

    public function encode($data)
    {
        $hashh = base64_encode($data);
        return $hashh;
    }

    public function decode($hashh)
    {
        $data = base64_decode($hashh);
        return $data;
    }


    public function send_verification_email($email, $link, $email_subject = "Account Verification", $from = "accounts@site.com" )
    {
        $to = $email;
        $reply_to = $this->support_email;
        $request_message = 'Please click the following URL to activate your account:<br />'.
        $link.'<br />
        If clicking the URL above does not work, copy and paste the URL into a browser window.';
    
    
        // global $email_from,$email_subject;
        $headers = 'From: '.$this->site_name.' <'.$this->accounts_email . ">\r\n" .
            'Reply-To: '.$reply_to . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html\r\n";
    
        mail($to, $email_subject, $request_message, $headers);
    
    }

    public function check_empty($param, $redirect, $message = 'An error occurred')
    {
        if (empty($param))
        {
            \Session::flash('flash_message', $message);
            if ($redirect == 'back_redir')
            {
                return redirect()->back();
            }
            return redirect($redirect);
        }
    }


}

