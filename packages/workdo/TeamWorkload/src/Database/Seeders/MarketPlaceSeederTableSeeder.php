<?php

namespace Workdo\TeamWorkload\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\LandingPage\Entities\MarketplacePageSetting;


class MarketPlaceSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $module = 'TeamWorkload';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Team Workload';
        $data['product_main_description'] = '<p>Team Workload Module is a process of distributing work among employees and monitoring people’s utilization over time. The goal is to make sure the work is delivered within the planned time frame, but also to keep a healthy balance in terms of the amount of work every team member needs to do.
        </p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Team Workload Is Useful';
        $data['dedicated_theme_description'] = '<p>With the Team Workload module, we can find out the total number of hours an employee has the ability to work in a week and the total number of hours worked in these occupations.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Setup for Working","dedicated_theme_section_description":"<p>Implement a system to accurately track and record overtime hours. This can be done through the same timesheet system, but overtime hours must be clearly distinguished from regular work hours. Apart from this we can also set the working hours of the day for a particular user and holidays for the user. when you set holiday for user at that you can see your users estiamted time and Spent time show diffrent.The main advantage of this module is that you can ensure a balanced workload and better productivity..<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null},"2":{"title":"null","description":null},"3":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Reporting and Analytics","dedicated_theme_section_description":" <p>Team Workload report provides total capacity ,total estimated time and total avaliable capacity and also you can show particular users Total Capcity Hours in using this report.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null},"2":{"title":null,"description":null},"3":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Team Workload"},{"screenshots":"","screenshots_heading":"Team Workload"},{"screenshots":"","screenshots_heading":"Team Workload"},{"screenshots":"","screenshots_heading":"Team Workload"}]';
        $data['addon_heading'] = 'Why choose dedicated modulesfor Your Business?';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Why choose dedicated modules for Your Business?';
        $data['whychoose_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['pricing_plan_heading'] = 'Empower Your Workforce with DASH';
        $data['pricing_plan_description'] = '<p>Access over Premium Add-ons for Accounting, HR, Payments, Leads, Communication, Management, and more, all in one place!</p>';
        $data['pricing_plan_demo_link'] = '#';
        $data['pricing_plan_demo_button_text'] = 'View Live Demo';
        $data['pricing_plan_text'] = '{"1":{"title":"Pay-as-you-go"},"2":{"title":"Unlimited installation"},"3":{"title":"Secure cloud storage"}}';
        $data['whychoose_sections_status'] = 'on';
        $data['dedicated_theme_section_status'] = 'on';

        foreach($data as $key => $value){
            if(!MarketplacePageSetting::where('name', '=', $key)->where('module', '=', $module)->exists()){
                MarketplacePageSetting::updateOrCreate(
                [
                    'name' => $key,
                    'module' => $module

                ],
                [
                    'value' => $value
                ]);
            }
        }
    }
}
