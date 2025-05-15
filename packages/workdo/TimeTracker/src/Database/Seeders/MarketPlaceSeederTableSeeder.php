<?php

namespace Workdo\TimeTracker\Database\Seeders;

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
        $module = 'TimeTracker';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Time Tracker';
        $data['product_main_description'] = '<p>The Time Tracker Module in Dash SaaS redefines the way businesses manage their workforce, offering a suite of tools designed to enhance productivity, transparency, and accountability. With seamless task tracking, managers gain real-time visibility into ongoing activities, promoting efficient project management and a culture of responsibility within the organization.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Track, Capture, Elevate: Dash SaaS Time Tracker';
        $data['dedicated_theme_description'] = '<p>Effortlessly manage tasks, capture real-time insights, and optimize project time with Dash SaaS\'s Time Tracker. Enhance transparency and boost efficiency in workforce management for unparalleled success.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Visualizing Productivity","dedicated_theme_section_description":"<p>A standout feature of the Time Tracker Module is the ability to capture real-time screenshots of employees\' screens during tracker engagement. This powerful tool provides managers with immediate insights into work environments, aiding in task evaluation, and offering targeted support. The visual representation facilitates the identification of bottlenecks and areas for improvement.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Strategic Time Monitoring","dedicated_theme_section_description":"<p>The Time Tracker Module empowers organizations with the capability to monitor time spent on specific tasks, contributing to accurate project planning and resource allocation. This feature also facilitates efficient client billing by providing transparent and detailed breakdowns of time invested in various project components.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Transparency and Accountability","dedicated_theme_section_description":"<p>By offering real-time monitoring capabilities, the Time Tracker Module transforms workforce management into a transparent and data-driven process. This dynamic view enhances overall productivity and facilitates timely decision-making based on the latest insights. Additionally, providing employees with visibility into how their time contributes to overarching goals encourages a sense of ownership and responsibility.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Comprehensive Solution","dedicated_theme_section_description":"<p>Dash SaaS\'s Time Tracker Module emerges as a comprehensive solution for businesses aiming to optimize their workforce management strategies. Its combination of task tracking, screen capture, and time monitoring features positions it as a valuable asset in fostering a culture of efficiency, transparency, and accountability within the organization.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"TimeTracker"},{"screenshots":"","screenshots_heading":"TimeTracker"},{"screenshots":"","screenshots_heading":"TimeTracker"},{"screenshots":"","screenshots_heading":"TimeTracker"},{"screenshots":"","screenshots_heading":"TimeTracker"}]';
        $data['addon_heading'] = 'What is Lorem Ipsum?';
        $data['addon_description'] = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'What is Lorem Ipsum?';
        $data['whychoose_description'] = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
        $data['pricing_plan_heading'] = 'What is Lorem Ipsum?';
        $data['pricing_plan_description'] = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
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
