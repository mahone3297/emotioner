<?php

// @date 2012-12-09
// @author mahone

class EmpHttp
{
    const GET = 'GET';
    const POST = 'POST';

    private $errno = 0;
    private $error = '';

    public function __construct()
    {
    }

    // get
    public function get(Array $urls, $options=null)
    {
        return $this->commonHttp(self::GET, $urls, $options);
    }

    // post
    public function post(Array $urls, $data, $options=null)
    {
        return $this->commonHttp(self::POST, $urls, $options, $data);
    }

    // @access: private
    private function commonHttp($action, Array $urls, $options=null, $data=null)
    {
        if (count($urls) === 1) {
            $ch = curl_init();

            // 暂时写死
            $opt = array(
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 180
            );

            switch ($action) {
                case self::GET:
                    break;
                case self::POST:
                    $opt[CURLOPT_POST] = true;
                    $opt[CURLOPT_POSTFIELDS] = $data;
                    break;
                default:
                    break;
            }
            if (isset($options) && is_array($options)) {
                foreach ($options as $key => $val) {
                    $opt[$key] = $val;
                }
            }
            $opt[CURLOPT_URL] = $urls[0];
            curl_setopt_array($ch, $opt);

            $result = curl_exec($ch);
            if ($result === false) {
                $this->errno = curl_errno($ch);
                $this->error = curl_error($ch);
            }
            curl_close($ch);

            return $result;
        } else {
            // multi http 待实现
            return false;
        }
    }

    // get curl errno
    public function errno()
    {
        return $this->errno;
    }

    // get curl error
    public function error()
    {
        return $this->error;
    }
}
