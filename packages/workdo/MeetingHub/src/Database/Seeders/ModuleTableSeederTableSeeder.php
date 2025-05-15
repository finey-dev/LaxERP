<?php

namespace Workdo\MeetingHub\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\MeetingHub\Entities\MeetingHubModule;

class ModuleTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $sub_module = [
            'Client', 'Vendor'
        ];


        foreach ($sub_module as $sm) {

            $check = MeetingHubModule::where('module', 'Account')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'Account';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Client') {
                    $new->model_name = $sm;
                }
                if ($sm == 'Vendor') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }
        $sub_module = [
            'Lead'
        ];
        foreach ($sub_module as $sm) {
            $check = MeetingHubModule::where('module', 'Lead')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'Lead';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Lead') {
                    $new->model_name = $sm;
                }
                if ($sm == 'Deal') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }

        $sub_module = [
            'Employee'
        ];
        foreach ($sub_module as $sm) {
            $check = MeetingHubModule::where('module', 'Hrm')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'Hrm';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Employee') {
                    $new->model_name = $sm;
                }
                if ($sm == 'Award') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }

        $sub_module = [
            'Teacher'
        ];
        foreach ($sub_module as $sm) {
            $check = MeetingHubModule::where('module', 'MusicInstitute')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'MusicInstitute';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Teacher') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }
        $sub_module = [
            'Parent'
        ];
        foreach ($sub_module as $sm) {
            $check = MeetingHubModule::where('module', 'School')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'School';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Parent') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }
        $sub_module = [
            'Advocate'
        ];
        foreach ($sub_module as $sm) {
            $check = MeetingHubModule::where('module', 'LegalCaseManagement')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'LegalCaseManagement';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Advocate') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }
        $sub_module = [
            'Agriculture User'
        ];
        foreach ($sub_module as $sm) {
            $check = MeetingHubModule::where('module', 'AgricultureManagement')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'AgricultureManagement';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Agriculture User') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }
        $sub_module = [
            'Tenants'
        ];
        foreach ($sub_module as $sm) {
            $check = MeetingHubModule::where('module', 'PropertyManagement')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'PropertyManagement';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Tenants') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }
        $sub_module = [
            'Doctor'
        ];
        foreach ($sub_module as $sm) {
            $check = MeetingHubModule::where('module', 'HospitalManagement')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'HospitalManagement';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Doctor') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }

        $sub_module = [
            'Agent', 'Journalist'
        ];


        foreach ($sub_module as $sm) {

            $check = MeetingHubModule::where('module', 'Newspaper')->where('submodule', $sm)->first();
            if (!$check) {
                $new = new MeetingHubModule();
                $new->module = 'Newspaper';
                $new->submodule = $sm;
                $new->type = 'company';
                if ($sm == 'Agent') {
                    $new->model_name = $sm;
                }
                if ($sm == 'Journalist') {
                    $new->model_name = $sm;
                }
                $new->save();
            }
        }
    }
}
