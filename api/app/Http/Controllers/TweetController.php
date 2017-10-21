<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use DB;
use Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class TweetController extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function insert(){


    $api_key = Request::get('api_key');
    $tweet = Request::get('tweet');
    $user_id = DB::table('users')->where('api_key',$api_key)->value('id');
    $name = DB::table('users')->where('api_key',$api_key)->value('name');
    $data = array('user_id' =>$user_id ,'message' =>$tweet ,'name' => $name);
    $result = DB::table('tweets')->insert($data);

    if($result>0){

      $status['status'] = 'success';
      $status['error'] = 'false';


      return json_encode($status);

    }else {
      $status['status'] = 'fail';

      return json_encode(([$status,'data'=>'']));
    }

    }

    public function stream(){

      $api_key = Request::get('api_key');
      $user_id = DB::table('users')->where('api_key',$api_key)->value('id');
      $following = DB::table('users')->where('id',$user_id)->value('following');
      $following = explode(',',$following);

      $data = array();
        $c=0;
      for($i =0 ; $i<count($following);$i++){
        $sql = 'select * from tweets where user_id="'.$following[$i].'"';
        // $tweetq = DB::table('tweets')->select('name','message','time')->where('user_id',$following[$i]);
        // dd($tweetq);
        $tweet=DB::select(DB::raw($sql));
        // dd($rr);

        $array = json_decode(json_encode($tweet), True);

        foreach ($array as $row) {



          $data[$c]['name']=$row['name'];
          $data[$c]['tweet']=$row['message'];
          $data[$c]['timestamp']=$row['time'];
          $c++;
        }

        return json_encode((['stream'=>$data]));

      }

    }

}
