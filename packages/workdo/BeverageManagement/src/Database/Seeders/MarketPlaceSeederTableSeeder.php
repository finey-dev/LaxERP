<?php

namespace Workdo\BeverageManagement\Database\Seeders;

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
        $module = 'BeverageManagement';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Beverages Production System';
        $data['product_main_description'] = '<p>Optimize your beverage production process by efficiently managing collection centers. Dash SaaS allows you to oversee and coordinate the collection of raw materials from various sources, ensuring a seamless supply chain.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Dash SaaS Beverage Module';
        $data['dedicated_theme_description'] = '<p>Optimize beverage production from collection to packaging with Dash SaaS. Manage raw materials, streamline manufacturing, and perfect packaging details for unparalleled efficiency and quality. Elevate your beverage business with a user-friendly interface and powerful features.</p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Raw Materials Control",
                "dedicated_theme_section_description": "<p>Take control of your ingredients with Dash SaaS raw materials management feature. Easily track and manage the inventory of essential components, ensuring you have a real-time overview of your stock levels for informed decision-making. You can also “Add Stock” with the help of this module if you want.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Bill of Materials (BoM)",
                "dedicated_theme_section_description": "<p>Craft the perfect recipe for success with the Bill of Materials functionality. Define and organize the exact quantities of each ingredient required for your beverage production, ensuring consistency and quality in every batch.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Manufacturing Details",
                "dedicated_theme_section_description": "<p>Streamline your manufacturing processes with Dash SaaS. Track production timelines, monitor equipment usage, and manage the workforce efficiently. Gain insights into the entire manufacturing workflow to optimize productivity.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image":"",
                "dedicated_theme_section_heading":"Packaging Details",
                "dedicated_theme_section_description":"<p>Present your beverages in the best light with the Packaging Details feature. From labeling to packaging materials, Dash SaaS allows you to meticulously manage the finer details of how your products are presented to the market.<\/p>",
                "dedicated_theme_section_cards":{
                    "1":{
                        "title":null,
                        "description":null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"BeverageManagement"},{"screenshots":"","screenshots_heading":"BeverageManagement"},{"screenshots":"","screenshots_heading":"BeverageManagement"},{"screenshots":"","screenshots_heading":"BeverageManagement"},{"screenshots":"","screenshots_heading":"BeverageManagement"},{"screenshots":"","screenshots_heading":"BeverageManagement"}]';
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
