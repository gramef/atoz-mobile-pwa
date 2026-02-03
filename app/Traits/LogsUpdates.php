<?php

namespace App\Traits;

use App\AllUpdates;
use App\Traits\DeterminesUserType;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Auth;

trait LogsUpdates
{
    use DeterminesUserType;
public function logUpdate($job_id,$job_type, $user_id,$agent_id, $new_status, $comment = null)
    {
        $userId = Auth::id();
        $userType = $this->getUserType($userId);
        $code='';
        if($new_status==1)
        {$code='Rejected';}
        if($new_status==0)
        {$code='Matched';}
        if($new_status==2)
        {$code='Accepted';}
        if($new_status==3)
        {$code='Cancelled';}
        if($new_status==4)
        {$code='Assigned';}
        
        if(empty($agent_id)){ $agent_id=0;}
        
       
        try {

            AllUpdates::create([
                'job_id' => $job_id,
                'job_type'=>$job_type,
                'user_id' => $user_id,
                'user_type' => $userType,
                'agent_id'=>$agent_id,
                'new_status' => $new_status,
                'code'=>$code,
                'comment' => $comment,
                'update_time' => now(),
                'update_date' => now(),
                'deleted' => 'N',
            ]);
          
          } catch (\Exception $e) {
          print($e->getMessage());exit;
              return $e->getMessage();
          }
    
    }
        function objectToArray(&$object)
{
    return @json_decode(json_encode($object), true);
}
}