<?php

if (!function_exists('error_response')){
    function error_response($exception){
        return response()->json([
            'status' => 0,
            'message' => $exception->getMessage(),
            'data' => []
        ]);
    }
}


if (!function_exists('no_records')){
    function no_records($message){
        return response()->json([
            'status' => 1,
            'message' => $message,
            'data' => []
        ]);
    }
}


/**
	 * Datatable configuration
	 *
	 * @param req		As	Request Data
	 * @param res		As 	Response Data
	 * @param options	As Object of data have multiple values
	 *
	 * @return json
	 */
    function configDatatable($request,$formData=null){
		$resultDraw		= 	($request->draw)	? $request->draw : 1;
		$sortIndex	 	= 	($request->order && $request->order[0]['column'] != '') 	? 	$request->order[0]['column']		: '' ;
		$sortOrder	 	= 	($request->order && $request->order[0]['dir'] && ($request->order[0]['dir'] == 'asc')) ? 'ASC' :'DESC';
		
		/* Searching  */
		$conditions 		=	[];
		$searchData 		=	($request->columns) ? $request->columns :[];
		if(count($searchData) > 0){
			foreach ($searchData as $index => $record) {
				$fieldName 		= (isset($record['name']) ? $record['name'] : (isset($record['data']) ? $record['data'] : ''));
				$searchValue	= (isset($record['search']) && !empty($record['search']['value'])) ? trim($record['search']['value']) : '';
				$fieldType		= (isset($record['field_type'])) ? $record['field_type'] : '';
				if($searchValue && $fieldName){
					
					if(is_numeric($searchValue)){
						array_push($conditions, [$fieldName , '=',$searchValue]);
					}else{
						$valData    =   '%'.$searchValue.'%';
						array_push($conditions, [$fieldName , 'like',$valData]);
					}
				}
			}
		}

		/* Sorting */
		$sortConditions = [];
		if($sortIndex !=''){
			if($searchData[$sortIndex]){
				$dataVal				=	(isset($searchData[$sortIndex]['data']) ? $searchData[$sortIndex]['data'] : '');
				$orderFieldName 		=   (isset($searchData[$sortIndex]['name']) ? $searchData[$sortIndex]['name'] : $dataVal);
				$proptyType				=	'data';
				if(isset($searchData[$sortIndex]['name']) && $searchData[$sortIndex]['name']){
					$proptyType				=	'name';
				}
				if(isset($searchData[$sortIndex][$proptyType]) && !empty($searchData[$sortIndex][$proptyType])){
					$sortConditions[$searchData[$sortIndex][$proptyType]] = $sortOrder;
				}
			}
		}else{
			$sortConditions['id'] = $sortOrder;
		}
		
		return [
			'sort_conditions' 	=> $sortConditions,
			'conditions' 		=> $conditions,
			'result_draw' 		=> $resultDraw
		];
	
}//End configDatatable()



	/*
     * Function to save notification and activity logs
     *
     * @param $tyep as
     *
     * @return null
    * */
	 function saveNotificationActivity($rep_Array, $action, $user_id = 0,$otherPrams=null){
        $notification_template = \App\Models\NotificationTemplate::where('action', $action)->first();
        if (!empty($notification_template)) {
            $notification_template  = $notification_template->toArray();

            $template_action        = $notification_template['action'];

            $notification_action    = \App\Models\Notifications_Action::where('action', $template_action)->first();

            $cons       = explode(',',$notification_action->option);

            $constants  = array();

            foreach ($cons as $key => $val) {
                $constants[] = '{' . $val . '}';
            }
            
            // $message = str_replace($constants, $rep_Array, $notification_template['body']);

            $message                                = $rep_Array[0] ?? '';
       
            $notificationData                       = new \App\Models\Notifications;
            $notificationData->user_id              = $user_id;
            $notificationData->notification         = $message;
            if($otherPrams != null){
                $notification_template['subject']         =  $otherPrams;

            }
            $notificationData->action         		= $template_action;
            $notificationData->is_read              = Config::get('app.NOT_READ');
            $notificationData->other_params         = $otherPrams;
            $userDetails 	=	\App\Models\User::where('id',$user_id)->first();
            if(isset($userDetails) && !empty($userDetails)){
            	if($userDetails->push_notification == 1){
                    if ($action != "USER_MESSAGE") {
                        $notificationData->save();
                    }
            		sendPushNotification($notification_template['subject'],$message,$notificationData->id,$user_id);
            	}
            }
            
        }   
    }//end saveNotificationActivity()


    /*
     * Function to save notification and activity logs
     *
     * @param $tyep as
     *
     * @return null
    * */
	 function sendGroupNotification($rep_Array, $action, $members_id = null,$otherPrams=null,$group_id=0){
        $notification_template = \App\Models\NotificationTemplate::where('action', $action)->first();
        if (!empty($notification_template)) {
            $notification_template  = $notification_template->toArray();

            $template_action        = $notification_template['action'];

            $notification_action    = \App\Models\Notifications_Action::where('action', $template_action)->first();

            $cons       = explode(',',$notification_action->option);

            $constants  = array();

            foreach ($cons as $key => $val) {
                $constants[] = '{' . $val . '}';
            }
            $message                                = $rep_Array;
            if($otherPrams != null){
                $notification_template['subject']         =  $otherPrams;

            }

            // $is_mute = \App\Models\MuteConversation::where('chat_id',$chat_id)->where('deleted_by',$request['receiver_id'])->first();


            foreach($members_id as $k => $id ){
                $is_mute = \App\Models\MuteConversation::where('group_id',$group_id)->where('deleted_by',$id)->first();
                if($is_mute == ""){
                    $userDetails 	=	\App\Models\User::where('id', $id)->first();
                    if (isset($userDetails) && !empty($userDetails)) {
                        if ($userDetails->push_notification == 1) {
                            sendPushNotification($notification_template['subject'], $message, 0, $id);
                        }
                    }
                }
            }
            
            // // $message = str_replace($constants, $rep_Array, $notification_template['body']);
            // $message                                = $rep_Array[0] ?? '';
            // $notificationData                       = new \App\Models\Notifications;
            // $notificationData->user_id              = $user_id;
            // $notificationData->notification         = $message;
            // if($otherPrams != null){
            //     $notification_template['subject']         =  $otherPrams;

            // }
            // $notificationData->action         		= $template_action;
            // $notificationData->is_read              = Config::get('app.NOT_READ');
            // $notificationData->other_params         = $otherPrams;
            // $userDetails 	=	\App\Models\User::where('id',$user_id)->first();
            // if(isset($userDetails) && !empty($userDetails)){
            // 	if($userDetails->push_notification == 1){
            // 		$notificationData->save();

            // 		sendPushNotification($notification_template['subject'],$message,$notificationData->id,$user_id);
            // 	}
            // }
            
        }   
    }//end saveNotificationActivity()

    /*
     * Function to send Push Notification
     *
     * @param $itemDetails as item detail
     *
     * @return Count
     * */
	 function sendPushNotification($title="",$message="",$notificationId="",$user_id=""){

		$firebaseTokens     =   \App\Models\NotificationTokens::where('user_id',$user_id)
                                ->pluck('notification_token')
                                ->toArray();

        $SERVER_API_KEY 	= 	Config::get('app.SERVER_KEY');
        // $data = [
        //     "registration_ids" => $firebaseTokens,
        //     "notification" => [
        //         "title" => $title,
        //         "body" 	=> strip_tags($message),  
        //     ]
        // ];


        $notification = [
            "title" => $title,
            "body" 	=> strip_tags($message), 
            'icon' =>'myIcon', 
            'sound' => 'mySound'
        ];

        $extraNotificationData = ["message" => $notification,"notification_id" =>$notificationId];

        $data = [
            //"to" => $firebaseTokens,//single token 
            'registration_ids' 	=> $firebaseTokens, //multple token array
            'notification' 		=> $notification,
            'data' 				=> $extraNotificationData
        ];


        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
  
        return $response;
	}