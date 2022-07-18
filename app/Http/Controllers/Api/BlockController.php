<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Block;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\ChatUser;
use App\Models\Follower;


class BlockController extends Controller
{
    
    public function blockAndUnblock(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
    
        try {
            $block = Block::where(['block_by_id' =>auth('api')->user()->id,
                                'block_to_id' => $request['user_id']])->first();
            $profile_id = auth('api')->user()->id;
            $block_id = $request['user_id'];             
            if (empty($block)) {
                $block = new Block();
                $block->block_by_id = auth('api')->user()->id;
                $block->block_to_id = $request['user_id'];
                $block->save();
                $update_data = ChatUser::orWhere(function ($query) use($profile_id,$block_id) {
                    $query->where('sender_id', $profile_id)
                        ->where('receiver_id',$block_id);
                })->orWhere(function ($query) use($profile_id,$block_id) {
                    $query->where('sender_id', $block_id)
                        ->where('receiver_id', $profile_id);
                })->update(['is_block' => 1]);
                
                $unfollow = Follower::where(['follower_id' =>auth('api')->user()->id,
                'following_id' => $block_id])->delete();

                $removefollowing = Follower::where(['follower_id' =>$block_id,
                'following_id' => auth('api')->user()->id])->delete();

                return response()->json([
                    'status' => 1,
                    'message' => 'User blocked successfully',
                    'data' => $block
                ]);
            }
            $block->delete();
            $update_data = ChatUser::orWhere(function ($query) use($profile_id,$block_id) {
                $query->where('sender_id', $profile_id)
                    ->where('receiver_id',$block_id);
            })->orWhere(function ($query) use($profile_id,$block_id) {
                $query->where('sender_id', $block_id)
                    ->where('receiver_id', $profile_id);
            })->update(['is_block' => 0]);

            return no_records('User Unblock successfully');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }



    }

    public function blockList(){
        try {
            $block = Block::where('block_by_id',auth('api')->user()->id)->with('blockList:id,image,firstname,lastname,username')->get();
            // if ($block->isNotEmpty()){
        //  $block = User::where('id',auth('api')->user()->id)->with('userBlockList')->get();
            if ($block->isNotEmpty()){

                return response()->json([
                    'status' => 1,
                    'message' => 'Block List data.',
                    'data' => $block
                ]);
            }
            return no_records('No Records');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }

    

    
}
