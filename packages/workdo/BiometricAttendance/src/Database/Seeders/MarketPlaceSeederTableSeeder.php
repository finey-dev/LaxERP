<?php

namespace Workdo\BiometricAttendance\Database\Seeders;

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
        $module = 'BiometricAttendance';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'BiometricAttendance';
        $data['product_main_description'] = '<p>Say goodbye to manual attendance tracking and hello to efficiency. With our Biometric Attendance Module, you can easily manage the punch-in and punch-out times of your employees. Our user-friendly interface allows you to view real-time attendance status, ensuring you always have up-to-date information at your fingertips.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Efficient Time Tracking';
        $data['dedicated_theme_description'] = '<p>Implementing biometric attendance streamlines time tracking and enhances security.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"System Settings","dedicated_theme_section_description":"","dedicated_theme_section_cards":{"1":{"title":"Convenient System Settings","description":"Customize your attendance management system to suit your organization\'s needs. Our system setup page allows you to configure various system settings With Dash SaaS, you have full control over how attendance is tracked and managed within your organization."},"2":{"title":"Enhanced Security and Accuracy","description":"Customize your attendance management system with Dash SaaS by configuring working hours, overtime rules, and holiday calendars, but exclusively available with ZKTeco software integration. By entering ZKTeco API credentials, an auth token is generated, enabling seamless integration with the Dash SaaS biometric attendance module. Once integrated, you gain full control over system settings, and attendance history is conveniently presented within Dash SaaS, providing comprehensive insights into your organization\'s attendance tracking."}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Attendance History","dedicated_theme_section_description":"","dedicated_theme_section_cards":{"1":{"title":"Comprehensive Attendance History","description":"Keep track of your employees\' attendance history with ease. Our module provides a detailed overview of each employee\'s attendance records, allowing you to monitor trends and identify any patterns or discrepancies. Whether you need daily, weekly, or monthly reports, Dash SaaS has you covered."},"2":{"title":"Seamless Data Sync with HRM Module","description":"Syncing attendance data with your HRM system has never been easier. With the click of a button, you can seamlessly transfer attendance records from the Biometric attendance module to the HRM module, ensuring consistency across all your HR processes. Our sync feature saves you time and eliminates the need for manual data entry, reducing the risk of errors."}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"BiometricAttendance"},{"screenshots":"","screenshots_heading":"BiometricAttendance"},{"screenshots":"","screenshots_heading":"BiometricAttendance"}]';
        $data['addon_heading'] = 'Why choose dedicated modulesfor Your Business?';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Why choose dedicated modulesfor Your Business?';
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
