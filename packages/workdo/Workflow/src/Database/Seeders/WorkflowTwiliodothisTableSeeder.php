<?php

namespace Workdo\Workflow\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class WorkflowTwiliodothisTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $check = \Workdo\Workflow\Entities\Workflowdothis::where('submodule','Send Twilio Notification')->first();
        if(!$check){
            $new = new \Workdo\Workflow\Entities\Workflowdothis();
            $new->submodule = 'Send Twilio Notification';
            $new->module = 'Twilio';
            $new->type = 'company';
            $new->save();
        }
        // $this->call("OthersTableSeeder");
    }
}
