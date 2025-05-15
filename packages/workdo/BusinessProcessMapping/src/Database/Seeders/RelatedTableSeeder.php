<?php

namespace Workdo\BusinessProcessMapping\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\ModuleFacade as Module;
use Workdo\BusinessProcessMapping\Entities\Related;


class RelatedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this_module = Module::find('BusinessProcessMapping');
        $this_module->enable();

        $sub_module = [
            [
                'related' => 'Project',
                'model_name' => 'Project',
            ],
            [
                'related' => 'Task',
                'model_name' => 'Task',
            ],
            [
                'related' => 'Lead',
                'model_name' => 'Lead',
            ],
            [
                'related' => 'Deal',
                'model_name' => 'Deal',
            ],
            [
                'related' => 'Property',
                'model_name' => 'Property',
            ],
            [
                'related' => 'Contract',
                'model_name' => 'Contract',
            ],
            [
                'related' => 'Other',
                'model_name' => 'Other',
            ],
        ];

        foreach ($sub_module as $sm) {
            $check = Related::where('related', $sm['related'])->where('model_name', $sm['model_name'])->first();

            if (!$check) {
                Related::create([
                    'related' => $sm['related'],
                    'model_name' => $sm['model_name'],
                ]);
            }
        }
    }
}
