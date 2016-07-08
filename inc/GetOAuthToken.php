<?php
/**
 * OAuth認証
 */
class GetOAuthToken
{
    public $REQUEST_TOKEN_URL = '';
    public $AUTHORIZE_URL = '';
    public $ACCESS_TOKEN_URL = '';
    public $CONSUMER_KEY = '';
    public $CONSUMER_SECRET = '';

    public function __construct()
    {
        $domain = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
        $this->REQUEST_TOKEN_URL = $domain.'/oauth1/request';
        $this->AUTHORIZE_URL     = $domain.'/oauth1/authorize';
        $this->ACCESS_TOKEN_URL  = $domain.'/oauth1/access';

        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /**
     * リクエストトークンの取得
     */
    public function goToAuthorize($c_key, $c_secret)
    {
        $this->CONSUMER_KEY = $c_key;
        $this->CONSUMER_SECRET = $c_secret;

        $oauth = new \OAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET);
        $request_token = $oauth->getRequestToken($this->REQUEST_TOKEN_URL);

        if (!$request_token) {
            throw new Exception('リクエストトークンの取得に失敗');
        }

        $_SESSION['request_token'] = $request_token['oauth_token'];
        $_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];
        $_SESSION['consumer_key'] = $this->CONSUMER_KEY;
        $_SESSION['consumer_secret'] = $this->CONSUMER_SECRET;

        $params = ['oauth_token' => $request_token['oauth_token']];
        $path = $this->AUTHORIZE_URL . '?' . http_build_query($params);

        $this->redirect($path);
    }

    /**
     * アクセストークンの取得
     *
     * @return array アクセストークンのセットされたハッシュ
     */
    public function getAccessToken()
    {
        if(!array_key_exists('consumer_key', $_SESSION) || !array_key_exists('consumer_secret', $_SESSION)){
          throw new \Exception('アクセストークンの取得に失敗');
        }

        $oauth = new \OAuth($_SESSION['consumer_key'], $_SESSION['consumer_secret']);
        // アクセストークンの取得
        $oauth->setToken($_SESSION['request_token'], $_SESSION['request_token_secret']);
        $access_token = $oauth->getAccessToken($this->ACCESS_TOKEN_URL, $_GET['oauth_verifier']);

        if (!$access_token) {
            throw new \Exception('アクセストークンの取得に失敗');
        }

        unset($_SESSION['request_token']);
        unset($_SESSION['request_token_secret']);
        unset($_SESSION['consumer_key']);
        unset($_SESSION['consumer_secret']);

        return $access_token;
    }

    /**
     * リダイレクト
     *
     * @param string $path   リダイレクト先URL
     * @param int    $status ステータスコード
     */
    private function redirect($path, $status = 302)
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header("Location: {$path}", true, $status);
        exit;
    }
}
