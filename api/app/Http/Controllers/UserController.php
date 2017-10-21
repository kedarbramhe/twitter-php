<?php

namespace App\Http\Controllers;

use DB;
use Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class UserController extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function register(){
      $data = Request::all();
      $length = 16; // 16 Chars long
      $key = "";
      for ($i=1;$i<=$length;$i++)
      {
        $alph_from = 65;
        $alph_to = 90;
        $num_from = 48;
        $num_to = 57;
        $chr = rand(0,1)?(chr(rand($alph_from,$alph_to))):(chr(rand($num_from,$num_to)));
        if (rand(0,1)) $chr = strtolower($chr);
        $key.=$chr;
      }
      $result;
      $password = password_hash(Request::get('password'), PASSWORD_DEFAULT);
      $name=Request::get('name');
      $email=Request::get('email');
      $dob=Request::get('dob');

      // check if user exists

      $user = DB::table('users')->where('email',$email)->first();

      if($user!=null){

        $status['status']="fail";
        $status['error']="true";
        return json_encode([$status,'data'=>"users already exists"]);

      }
      else{
        $length =25; // 16 Chars long
        $token = "";
        for ($i=1;$i<=$length;$i++)
        {
          $alph_from = 65;
          $alph_to = 90;
          $num_from = 48;
          $num_to = 57;
          $chr = rand(0,1)?(chr(rand($alph_from,$alph_to))):(chr(rand($num_from,$num_to)));
          if (rand(0,1)) $chr = strtolower($chr);
          $token.=$chr;
        }

        $data = array(
          'name' => $name,
          'email' => $email,
          'password' => $password,
          'dob' => $dob,
          'api_key' => $key
        );
        $result = DB::table('users')->insert($data);
        if($result>0){
          $last_user = DB::table('users')->select('name','email','api_key')->where('api_key',$key)->first();
          $status['status'] = 'success';
          $status['error'] = 'false';
          $status['data'] = 'data';

          return json_encode($status);

        }else {
          $status['status'] = 'fail';

          return json_encode(([$status,'data'=>'']));
        }
      }
    }

    public function login(){
      $data = Request::all();
      $email = Request::get('email');
      $password = Request::get('password');
      $db_password = DB::table('users')->where('email',$email)->value('password');
      if(password_verify($password,$db_password)!=true){
        $status['status']="fail";
        $status['message']="Not found";

        return json_encode($status);
      }else{

        $user = DB::table('users')->select('name','email','dob','api_key')->where('email',$email)->first();
        $status['status']="success";
        $status['error']="false";
        $status['message']="user exists";
        $status['data']=$user;

        return json_encode($status);

      }

    }

    public function profile(){

      $id = Request::get('id');

      $user = DB::table('users')->select('name','about','hobbies','followers_count','following_count')->where('id',$id)->first();

      $status['status']="success";
      $status['error']="false";
      $status['message']="user exists";
      $status['data']=$user;

      return json_encode($status);

    }

    public function follow(){

      $user_id = Request::get('id');
      $api_key = Request::get('api_key');

      $follower_id = DB::table('users')->where('api_key',$api_key)->value('id');

      // update the follwer

      $following = DB::table('users')->where('id',$follower_id)->value('following');
      if($following!=null)
      $following = $following.','.$user_id;
      else
      $following = $user_id;
      $following_count = DB::table('users')->where('id',$follower_id)->value('following_count');
      $following_count = intval($following_count) +1;
      DB::table('users')->where('id',$follower_id)->update(array('following'=>$following,'following_count'=>$following_count));

      // update the following

      $followers = DB::table('users')->where('id',$user_id)->value('followers');
      if($followers!=null)
      $followers = $followers.','.$follower_id;
      else
      $followers = $follower_id;
      $followers_count = DB::table('users')->where('id',$user_id)->value('followers_count');
      $followers_count = intval($followers_count) +1;
      DB::table('users')->where('id',$user_id)->update(array('followers'=>$followers,'followers_count'=>$followers_count));

      $status['status']="success";
      $status['error']="false";
      $status['message']="unfollowed";
      $status['followers_count']=$followers_count;
      return json_encode($status);


    }

    public function unFollow(){
      $user_id = Request::get('id');
      $api_key = Request::get('api_key');

      $follower_id = DB::table('users')->where('api_key',$api_key)->value('id');

      // update the follwer

      $following = DB::table('users')->where('id',$follower_id)->value('following');

      $following = explode(',',$following);
      $following = array_diff($following,array($user_id));
      $following = implode(',',$following);
      $following_count = DB::table('users')->where('id',$follower_id)->value('following_count');
      $following_count = intval($following_count) -1;
      DB::table('users')->where('id',$follower_id)->update(array('following'=>$following,'following_count'=>$following_count));

      // update the following

      $followers = DB::table('users')->where('id',$user_id)->value('followers');

      $followers = explode(',',$followers);
      $followers = array_diff($followers,array($follower_id));
      $followers = implode(',',$followers);
      $followers_count = DB::table('users')->where('id',$user_id)->value('followers_count');
      $followers_count = intval($followers_count) -1;
      DB::table('users')->where('id',$user_id)->update(array('followers'=>$followers,'followers_count'=>$followers_count));

      $status['status']="success";
      $status['error']="false";
      $status['message']="unfollowed";
      $status['followers_count']=$followers_count;
      return json_encode($status);

    }
    }
