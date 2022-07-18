<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;
use App\Models\GroupMember;

class DailyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for update the database of group (active or deactive)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    
        // $yesterday = Timer::whereDate('created_at', Carbon::yesterday())->get();

        $yesterday = date("Y-m-d", strtotime( '-1 days' ) );
        //group activate deactive 
        $groups = Group::with(['onlyOneGroupChat' => function($query) use ($yesterday){
            $query->whereDate('created_at',$yesterday);
        }])->where('is_active',1)->where('is_inactive_group',1)->get();
        foreach($groups as $ky => $group){
            if($group->onlyOneGroupChat == null){
                $group->update([
                    'is_active' => 0
                ]);
            }
        }
        //group user activate or deactive

        $group_members= GroupMember::with(['groups.userChat' => function($query) use ($yesterday){
            $query->whereDate('created_at',$yesterday);
        }])->get();
        foreach ($group_members as $key => $group_member) {
            if($group_member->groups->is_inactive_member != "0"){
                if(count($group_member->groups->userChat) == 0 && $group_member->is_admin != "1" && $group_member->is_accept == "1") {
                    $group_member->delete();
                }
            }
        }

        
    }



}
