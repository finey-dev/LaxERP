<?php

namespace Workdo\Procurement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ProcurementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(PermissionTableSeeder::class);
        if(module_is_active('LandingPage'))
        {
            $this->call(MarketPlaceSeederTableSeeder::class);
        };
        if(module_is_active('AIAssistant'))
        {
            $this->call(AIAssistantTemplateListTableSeeder::class);
        }
    }
}