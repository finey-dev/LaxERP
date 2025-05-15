<?php

namespace Workdo\Facilities\Database\Seeders;

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
        $module = 'Facilities';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Facilities';
        $data['product_main_description'] = "<p>The Facilities Module in Dash SaaS is designed to enhance and streamline resource management for businesses of all sizes. This powerful tool provides a centralized platform for managing various facilities-related tasks, ensuring efficient use of resources and optimal operational performance. Whether you're managing office spaces, equipment, or other assets, the Facilities Module offers a comprehensive solution to simplify and automate these processes.</p>";
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Optimize resource management with the Facilities Module for streamlined operations';
        $data['dedicated_theme_description'] = '<p>With the Facilities Module, businesses can effectively manage their resources in a centralized and organized manner. This module allows users to track the allocation and usage of resources such as meeting rooms, equipment, and other assets.</p>';
        $data['dedicated_theme_sections'] = '[{
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Enhanced Scheduling and Booking",
                "dedicated_theme_section_description": "<p>The Facilities Module offers robust scheduling and booking capabilities, making it easy for users to reserve resources as needed. This feature helps avoid conflicts and double bookings, ensuring that resources are available when required. Users can quickly check availability and make reservations through a user-friendly interface, streamlining the process and saving valuable time. Automated notifications and reminders further enhance the scheduling experience, reducing the likelihood of missed appointments or unutilized resources.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Comprehensive Reporting and Analytics",
                "dedicated_theme_section_description": "<p> Effective resource management requires detailed insights and data analysis, and the Facilities Module delivers just that. Businesses can generate comprehensive reports on resource usage, identify trends, and make informed decisions to improve operational efficiency. The analytics provided by the Facilities Module enable businesses to identify underutilized resources, optimize allocation, and plan for future needs. These insights are crucial for driving continuous improvement and maximizing the value of available resources.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image":"",
                "dedicated_theme_section_heading":"Easy Integration and Support",
                "dedicated_theme_section_description":"<p>The Facilities Module is designed to seamlessly integrate with other modules within Dash SaaS, providing a cohesive and unified platform for managing various aspects of your business. The integration process is straightforward, supported by detailed documentation and a dedicated support team to assist with any challenges. Whether you need help with initial setup or ongoing maintenance, reliable support is available to ensure a smooth and successful integration. This ease of integration and robust support makes the Facilities Module an invaluable tool for businesses looking to enhance their resource management capabilities.<\/p>",
                "dedicated_theme_section_cards":{
                    "1":{
                        "title":null,
                        "description":null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Facilities"},{"screenshots":"","screenshots_heading":"Facilities"},{"screenshots":"","screenshots_heading":"Facilities"},{"screenshots":"","screenshots_heading":"Facilities"},{"screenshots":"","screenshots_heading":"Facilities"},{"screenshots":"","screenshots_heading":"Facilities"}]';
        $data['addon_heading'] = '<h2>Why choose dedicated modules<b> for Your Business?</b></h2>';
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
