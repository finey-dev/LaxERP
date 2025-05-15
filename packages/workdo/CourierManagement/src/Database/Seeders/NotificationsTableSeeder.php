<?php

namespace Workdo\CourierManagement\Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NotificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // email notification
        $notifications = [
            'New Courier',
            'Courier Request Accept',
            'Courier Request Reject',
        ];
        $permissions = [
            'couriermanagement manage','couriermanagement manage','couriermanagement manage',
        ];
        foreach ($notifications as $key => $n) {
            $ntfy = Notification::where('action', $n)->where('type', 'mail')->where('module', 'CourierManagement')->count();
            if ($ntfy == 0) {
                $new = new Notification();
                $new->action = $n;
                $new->status = 'on';
                $new->permissions = $permissions[$key];
                $new->module = 'CourierManagement';
                $new->type = 'mail';
                $new->save();
            }
        }
    }
}
