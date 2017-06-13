<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\User as M_User;

class Index
    extends Controller
{
    protected $main_teamplate = 'Index';

    function __construct()
    {
        echo " __construct Controller Index <br/>";
        $this->M_User = new M_User();
    }


    function isPost($action)
    {

    }

    function index()
    {

        $client_id = '344999880236-avemoshdfile1s78mqugngj1suf0urrq.apps.googleusercontent.com'; // Client ID
        $client_secret = 'cudU-7d6CKd_fBZ-WD9Bvgtz'; // Client secret
        $redirect_uri = 'http://localhost/bookkeeping.com/index'; // Redirect URIs
        $url = 'https://accounts.google.com/o/oauth2/auth';

        $params = array(
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'client_id'     => $client_id,
            'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
        );

        echo $link = '<p><a href="' . $url . '?' . urldecode(http_build_query($params)) . '">Аутентификация через Google</a></p>';

        if (isset($_GET['code']))
        {
            $result = false;

            $params = array(
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code',
                'code'          => $_GET['code']
            );

            $url = 'https://accounts.google.com/o/oauth2/token';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);
            curl_close($curl);
            $tokenInfo = json_decode($result, true);

            if (isset($tokenInfo['access_token']))
            {
                $params['access_token'] = $tokenInfo['access_token'];
                $userInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo' . '?' . urldecode(http_build_query($params))), true);

                if (isset($userInfo['id']))
                {
                    $userInfo = $userInfo;
                    $result = true;
                }
            }
        }

        if(! empty($userInfo['email']))
        {
            $user = $this->M_User->getByEmail($userInfo['email']);

            if(empty($user))
            {
                // create new user
                $this->M_User->email = $userInfo['email'];
                $this->M_User->given_name = $userInfo['given_name'];
                $this->M_User->family_name = $userInfo['family_name'];
                $this->M_User->picture = $userInfo['picture'];
                $this->M_User->link = $userInfo['link'];
                $this->M_User->gender = $userInfo['gender'];

                $user_id = $this->M_User->save();

                // session create
                if(! $this->M_User->setSession($user_id))
                {
                    //echo 'false';
                    // todo false action
                }

                header('Location: /');

            } else {
                // session create
                if(! $this->M_User->setSession($user->id))
                {
                    //echo 'false';
                    // todo false action
                }

                header('Location: /');
            }
        } else {
            // redirect to homepage
            header('Location: /');

        }

    }
}