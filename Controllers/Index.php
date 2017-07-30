<?php
namespace bookkeeping\Controllers;
use bookkeeping\Controllers\Controller as Controller;
use bookkeeping\Models\User as M_User;

class Index
    extends Controller
{
    protected static $main_teamplate = 'Index';

    function __construct()
    {
        $this->M_User = new M_User();
    }


    function isPost($action)
    {

    }

    function logout()
    {
        $this->M_User->logout();
        header('Location: /');
    }

    function index()
    {
        $data['controller_name'] = self::getMainTeamplate();
        $this->content = $this->getView(self::getMainTeamplate() . '/Index.php', $data);
        $this->render();
    }

    function login()
    {


/*
        $client_id = '344999880236-avemoshdfile1s78mqugngj1suf0urrq.apps.googleusercontent.com'; // Client ID
        $client_secret = 'cudU-7d6CKd_fBZ-WD9Bvgtz'; // Client secret
        $redirect_uri = 'http://localhost/bookkeeping.com/index'; // Redirect URIs
        $redirect_uri = 'https://dudaml1986.000webhostapp.com/index'; // Redirect URIs
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
*/

$userInfo = array (
'id' => 110665724950086710524,
'email' => 'duda.ml1986@gmail.com',
'verified_email' => 1 ,
'name' => 'Denys Duvanov',
'given_name' => 'Denys ',
'family_name' => 'Duvanov' ,
'link' => 'https://plus.google.com/+DenysDuvanov',
'picture' => 'https://lh3.googleusercontent.com/-bTbce9eGjJU/AAAAAAAAAAI/AAAAAAAABKM/JgsGM1YiM_Y/photo.jpg',
'gender' => 'male ',
'locale' => 'ru' );

$userInfo3 = array (
    'id' => 110665724950086710524,
    'email' => 'chubaka_lenochka@gmail.com',
    'verified_email' => 1 ,
    'name' => 'Lenochka Duvanova',
    'given_name' => 'Lenochka ',
    'family_name' => 'Duvanova' ,
    'link' => 'https://plus.google.com/+LenochkaDuvanova',
    'picture' => 'http://s1.iconbird.com/ico/2013/12/505/w450h4001385925286User.png',
    'gender' => 'female ',
    'locale' => 'ru' );

// todo перевести email в нижний регистр
var_dump($userInfo);

        if(! empty($userInfo['email']))
        {
            $user = $this->M_User->getByEmail($userInfo['email'])[0];

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

                //header('Location: /');

            } else {
                // Если данный пользователь уже существует то создаём сессию
                $this->M_User->setSession($user->id);
                header('Location: /Pay/');
            }


        } else {
            // не удалось авторизоваться, вывод сообщения об ошибке.
            header('Location: /');
        }

    }
}