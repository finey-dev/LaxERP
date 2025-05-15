<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

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
        $module = 'RepairManagementSystem';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Repair Management System';
        $data['product_main_description'] = '<p>You are introducing the Repair Management Integration module in Dash SaaS, a cutting-edge solution designed to streamline your repair order processes and simplify invoicing tasks. With this module, you can access powerful tools that enhance efficiency and improve productivity in your repair operations.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = "Dash SaaS’s Repair Management Module";
        $data['dedicated_theme_description'] = '<p>Effortlessly manage repair orders and invoices in one place with Dash SaaS Repair Manager. Simplify your workflow and boost efficiency today!</p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Task Scheduling and Progress Monitoring",
                "dedicated_theme_section_description": "<p>With our intuitive repair order management system, you can easily track and organize requests as they come in. From identifying the parts that need repair to assigning tasks to technicians, our platform ensures that every detail is meticulously managed, allowing you to promptly and effectively address customer needs.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Hassle-Free Invoicing Solutions",
                "dedicated_theme_section_description": "<p>In addition to managing repair orders, our module offers robust scheduling and progress-tracking features. Whether you are planning repairs for multiple clients or monitoring the status of ongoing tasks, Dash SaaS provides real-time updates and notifications to keep you informed every step of the way.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image":"",
                "dedicated_theme_section_heading":"Detailed Payment Management",
                "dedicated_theme_section_description":"<p>But thats not all – our module also simplifies the invoicing process, allowing you to generate and manage invoices directly within the platform. Say goodbye to manual invoicing and juggling multiple systems. With Dash SaaS, you can create accurate invoices with ease, ensuring timely billing and improved cash flow management.<\/p>",
                "dedicated_theme_section_cards":{
                    "1":{
                        "title":null,
                        "description":null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"RepairManagementSystem"},{"screenshots":"","screenshots_heading":"RepairManagementSystem"},{"screenshots":"","screenshots_heading":"RepairManagementSystem"},{"screenshots":"","screenshots_heading":"RepairManagementSystem"},{"screenshots":"","screenshots_heading":"RepairManagementSystem"}]';
        $data['addon_heading'] = '<h2>Why choose dedicated modules<b> for Your Business?</b></h2>';
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


        foreach ($data as $key => $value) {
            if (!MarketplacePageSetting::where('name', '=', $key)->where('module', '=', $module)->exists()) {
                MarketplacePageSetting::updateOrCreate(
                    [
                        'name' => $key,
                        'module' => $module

                    ],
                    [
                        'value' => $value
                    ]
                );
            }
        }
    }
}
