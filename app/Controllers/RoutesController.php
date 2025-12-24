<?php

namespace App\Controllers;

use Dhiva\Core\DhivaAES;
use CodeIgniter\RESTful\ResourceController;

class RoutesController extends ResourceController
{
    private $headers;
    private $clientSecret;
    private $Authorization;
    private $publickey;
    public function decodeEndpointPost($url, $content = false)
    {
        if ($content) {
            $e = (DhivaAES::base64url_decode($url) . $content);
        } else {
            $e = (DhivaAES::base64url_decode($url));
        }
        $data = $this->httpPost($this->baseUrl() . 'index.php' . $e, $_POST);
        return $this->respond($data);
    }

    public function decodeEndpointGet($url, $content = false)
    {
        if ($content) {
            $e = (DhivaAES::base64url_decode($url) . $content);
        } else {
            $e = (DhivaAES::base64url_decode($url));
        }
        $data = $this->httpget($this->baseUrl() . 'index.php' . $e);
        return $this->respond($data);
    }
    public function decodeEndpointPut($url, $content = false)
    {
        if ($content) {
            $e = (DhivaAES::base64url_decode($url) . $content);
        } else {
            $e = (DhivaAES::base64url_decode($url));
        }
        $data = $this->httpPut($this->baseUrl() . 'index.php' . $e, $_POST);
        return $this->respond($data);
    }
    public function decodeEndpointDelete($url, $content = false)
    {
        if ($content) {
            $e = (DhivaAES::base64url_decode($url) . $content);
        } else {
            $e = (DhivaAES::base64url_decode($url));
        }
        $data = $this->httpdelete($this->baseUrl() . 'index.php' . $e);
        return $this->respond($data);
    }
    public function httpget($url)
    {
        $this->initHeader();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ($this->clientSecret && $this->Authorization) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'ClientSecret: ' . $this->clientSecret,
                'Authorization: ' . $this->Authorization
            ));
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }
    public function httpdelete($url)
    {
        $this->initHeader();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        if ($this->clientSecret && $this->Authorization) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'ClientSecret: ' . $this->clientSecret,
                'Authorization: ' . $this->Authorization
            ));
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }
    function httpPut($url, $params, $files = array())
    {
        $this->initHeader();
        $postData = $params;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        if ($this->clientSecret && $this->Authorization) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'ClientSecret: ' . $this->clientSecret,
                'Authorization: ' . $this->Authorization
            ));
        }
        $output = curl_exec($ch);
        if (!json_decode($output)) {
            print_r($output);
            die;
        }
        curl_close($ch);

        return json_decode($output);
    }
    function httpPost($url, $params, $files = array())
    {
        $this->initHeader();
        $postData = $params;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $isfile = $this->valids();
        if ($isfile) {
            $isfile = array_merge($postData, $isfile);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $isfile);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
        if ($this->clientSecret && $this->Authorization) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'ClientSecret: ' . $this->clientSecret,
                'Authorization: ' . $this->Authorization
            ));
        }
        $output = curl_exec($ch);
        if (!json_decode($output)) {
            print_r($output);
            die;
        }
        curl_close($ch);

        return json_decode($output);
    }
    private function initHeader()
    {
        $this->headers = getallheaders();
        if (!function_exists('str_contains')) {
            function str_contains($haystack, $needle)
            {
                return $needle !== '' && mb_strpos($haystack, $needle) !== false;
            }
        }
        if (isset($this->headers['Publickey'])) {
            $this->publickey = $this->headers['Publickey'];
        } else if (isset($this->headers['publickey'])) {
            $this->publickey = $this->headers['publickey'];
        }
        if (isset($this->headers['ClientSecret'])) {
            $this->clientSecret = $this->headers['ClientSecret'];
        } else if (isset($this->headers['clientSecret'])) {
            $this->clientSecret = $this->headers['clientSecret'];
        } else if (isset($this->headers['clientsecret'])) {
            $this->clientSecret = $this->headers['clientsecret'];
        } else if (isset($this->headers['Clientsecret'])) {
            $this->clientSecret = $this->headers['Clientsecret'];
        }
        if (isset($this->headers['Authorization'])) {
            $this->Authorization = $this->headers['Authorization'];
        } else if (isset($this->headers['authorization'])) {
            $this->Authorization = $this->headers['authorization'];
        }
    }
    public function encodeEndpoint($url)
    {
        print_r(DhivaAES::base64url_encode($url));
        die();
        redirect(DhivaAES::base64url_encode($url));
    }
    private function baseUrl()
    {
        $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
        $base_url .= "://" . $_SERVER['HTTP_HOST'];
        $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

        return $base_url;
    }
    public function notfound()
    {
        $response = [
            'status'   => false,
            'code' => HTTP_NOT_FOUND,
            'messages' => 'Not found'
        ];
        return $this->respond($response, HTTP_NOT_FOUND);
    }

    function valids()
    {
        $postFile = [];
        $a = 0;
        if (!empty($_FILES['file']['name'])) {
            $a = count($_FILES['file']['name']);
            $k = 0;
            for ($i = 0; $i < $a; $i++) {
                if (!empty($_FILES['file']['name'][$i])) {
                    $postFile['file[' . $i . ']'] = new \CURLFile(
                        $_FILES['file']['tmp_name'][$i],
                        $_FILES['file']['type'][$i],
                        $_FILES['file']['name'][$i]
                    );
                    $k++;
                }
            }
            if ($k !== $a) {
                return false;
            } else {
                $a = $a;
            }
        }
        return $postFile;
    }
}
