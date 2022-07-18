<?php

return [
    'buyer'             =>      0,
    'seller'            =>      1,
    'activate'          =>      1,
    'deactivate'        =>      0,
    'suspend'           =>      2,
    'emailTime'         =>      10,//expires in minutes
    'passwordTime'      =>      5,//expires in minutes    
    'mob_verify'        =>      1,
    'email_verify'      =>      1,
    'mob_not_verify'    =>      0,
    'email_not_verify'  =>      0, 
    'block'             =>      1,
    'unblock'           =>      0,
    'is_deleted'        =>      1,
    'is_not_deleted'    =>      0,    
    'shop_accepted'     =>      2,
    'shop_rejected'     =>      3,
    'shop_pending'      =>      1,  
    'shop_photo_path'   =>      base_path().DIRECTORY_SEPARATOR.'public/uploads/images'.DIRECTORY_SEPARATOR,
    'shop_photo_path1'  =>      base_path().DIRECTORY_SEPARATOR.'public/uploads/upl'.DIRECTORY_SEPARATOR,
    'image_path'        =>      config('app.url').'uploads/images'.DIRECTORY_SEPARATOR,
    'like'              =>      1,
    'unlike'            =>      0,
    'comment'           =>      1,
    'uncomment'         =>       0,       
];
