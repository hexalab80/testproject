<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Validator;
use App\User;
use App\ListInfo;
use App\ListDate;
use App\Reward;

class ListController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
          $this->user = Auth::user();
          return $next($request);
        });
    }

    public function add_list(Request $request){
      
      $user = User::find($this->user->id);
      if($user){
        $validate = Validator::make($request->all(), [
          'date' => 'required',
          ]);

        if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }

        $list_date_info = ListDate::where('user_id',$this->user->id)->where('date',date('Y-m-d', strtotime($request->date)))->first();

        if(!empty($list_date_info)){

          $list_date_id = $list_date_info->id;
        
        }else{
          $list_date_info1 = new ListDate;
          $list_date_info1->user_id = $this->user->id;
          $list_date_info1->date = date('Y-m-d', strtotime($request->date));
          $list_date_info1->created_at = date('Y-m-d H:i:s');
          $list_date_info1->updated_at = date('Y-m-d H:i:s');
          $list_date_info1->save();
          $list_date_id = $list_date_info1->id;
        }
        
        $stepsHistories = $request->stepsHistories;
        
        if($list_date_id){

          foreach ($stepsHistories as $key => $value) {
            
            $hour = $value['hour']; 
            $steps = $value['steps'];
            $timestamp = $value['timestamp'];

            $check_list_info = ListInfo::where('timestamp','!=',$timestamp)->where('hours',$hour)->where('list_date_id',$list_date_id)->orderBy('id','desc')->first();

            $check_list_info1 = ListInfo::where('timestamp',$timestamp)->where('hours',$hour)->where('list_date_id',$list_date_id)->orderBy('id','desc')->first();

            if($check_list_info){

              $check_list_info->steps = $steps;
              $check_list_info->timestamp = $timestamp;
              $check_list_info->updated_at = date('Y-m-d H:i:s');
              $check_list_info->save();

            }elseif(empty($check_list_info1)){

              $list_data = new ListInfo;
              $list_data->hours = $hour;
              $list_data->steps = $steps;
              $list_data->timestamp = $timestamp;
              $list_data->list_date_id = $list_date_id;
              $list_data->created_at = date('Y-m-d H:i:s');
              $list_data->updated_at = date('Y-m-d H:i:s');
              $list_data->save();
            }
          } 

          return response()->json(array('message' => 'List has been added successfully.'), 200);
        }
      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }

    public function fetchList(){

      //$date = date('Y-m-d', strtotime($request->date));
      
      $user = User::find($this->user->id);
      
      if(!empty($user)){
          
          $list_date_info = ListDate::where('user_id',$user->id)->orderBy('id','desc')->first();

          if(!empty($list_date_info)){
            
            $list_data = ListInfo::with('list')->where('list_date_id',$list_date_info->id)->orderBy('id','desc')->first();
            
            if(!empty($list_data)){
              
              return response()->json(array('status' => 'success',"last_list" => $list_data), 200);
           
            }else{
              return response()->json(array('status' => 'error'), 403);
            }
          }else{
            return response()->json(array('status' => 'error','message' => 'List not found.'), 403);
          }
          
      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }

    public function fetchAllListByUser(){
      $user = User::find($this->user->id);
      
      if(!empty($user)){
        
        $list_date_info = ListDate::with('listinfo')->where('user_id',$user->id)->get();
        return response()->json(array('status' => 'success',"list_data" => $list_date_info), 200);

      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }

    public function getMonthList($num){

       $user = User::find($this->user->id);
      
      if(!empty($user)){

        $current_date = date('Y-m-d');
        $lastDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d"))) . "-".$num." month" ) );

        $list_date_info = ListDate::with('listinfo')->whereBetween('date',[$lastDate, $current_date])->get();
        
        if(!empty($list_date_info)){
          return response()->json(array('status' => 'success','current_date' => $current_date,'lastDate' => $lastDate, 'list_data' =>$list_date_info), 200);
        }else{
          return response()->json(array('status' => 'error','message' => 'List not found.'), 403);
        }

      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }

    public function rewardlist(){
      
      $reward = Reward::all();

      return $reward;
    }


    public function getCoins(){
      
    }
}
