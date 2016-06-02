<?php
/**
 * A simple library component to
 * get fshare direct link with
 * vip account
 *
 * @author xtrung.net
 */
namespace xtrungnet\DirectLink;

use GuzzleHttp;
use Symfony\Component\DomCrawler\Crawler;

class FshareDirectLink
{

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    protected $loginGetUrl = 'https://www.fshare.vn';
    protected $loginPostUrl = 'https://www.fshare.vn/login';
    protected $downloadPostUrl = 'https://www.fshare.vn/download/get';
    protected $defaultRequestOptions = [
        'cookies' => true,
        'verify' => false,
    ];

    /**
     * Constructor.
     *
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Get fshare username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set fshare username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get fshare password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set fshare password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get Fshare direct download link
     *
     * @param string $link
     * @return string
     */
    public function getDownloadLink($link)
    {
        $client = new GuzzleHttp\Client($this->defaultRequestOptions);
        // get login csrf token
        $loginGetResult = $client->request('get',$this->loginGetUrl);
        $loginGetHtml = $loginGetResult->getBody()->getContents();
        $crawler = new Crawler($loginGetHtml);
        $loginToken = $crawler->filter("input[name='fs_csrf']")->attr('value');
        // post login
        $loginPostData = [
            'fs_csrf' => $loginToken,
            'LoginForm[email]' => $this->username,
            'LoginForm[password]' => $this->password,
            'LoginForm[checkloginpopup]' => 0,
            'LoginForm[rememberMe]' => 0,
            'yt0' => 'Đăng nhập',
        ];
        $client->request('post', $this->loginPostUrl, [
            'form_params' => $loginPostData,
        ]);
        // get download csrf token
        $downloadGetResult = $client->request('get', $link);
        $downloadHtml = $downloadGetResult->getBody()->getContents();
        $crawler = new Crawler($downloadHtml);
        $downloadToken = $crawler->filter("input[name='fs_csrf']")->attr('value');
        $linkCode = $this->getLinkCode($link);
        $downloadPostData = [
            'fs_csrf' => $downloadToken,
            'DownloadForm[pwd]' => '',
            'DownloadForm[linkcode]' => $linkCode,
            'ajax' => 'download-form',
        ];
        $downloadPostResult = $client->request('post', $this->downloadPostUrl, [
            'form_params' => $downloadPostData,
            'headers' => [
                'accept' => 'application/json, text/javascript, */*; q=0.01',
            ],
        ]);
        $downloadResponseContent = $downloadPostResult->getBody()->getContents();

        return json_decode($downloadResponseContent)->url;
    }

    /**
     * Get fshare code from link
     *
     * @param string $link
     * @return string
     */
    private function getLinkCode($link)
    {
        return trim(end(explode('/', $link)));
    }

}