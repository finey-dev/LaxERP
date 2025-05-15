<?php

namespace Workdo\FixEquipment\Database\Seeders;

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

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Fix Equipment';
        $data['product_main_description'] = '<p>The Fix Equipment System is a powerful solution for managing and tracking various types of equipment within your organization. It is designed to simplify the process of categorizing, auditing, and maintaining different equipment items.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = '<h2>Managing <b>Equipment</b></h2>';
        $data['dedicated_theme_description'] = '<p>Manage equipment effortlessly with Fix Equipment. Streamline tracking, maintenance, audits, and more, ensuring optimal efficiency and cost control for your organization.</p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Organizing Your Equipment",
                "dedicated_theme_section_description": "<p>Effortlessly organize your equipment with Fix Equipment management software.</p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": "Enhance Efficiency",
                        "description": "Streamline equipment management for improved productivity with Fix Equipment software."
                    },
                    "2": {
                        "title": "Efficient Audit",
                        "description": "Streamlined processes for comprehensive equipment auditing and compliance management."
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Asset & Maintenance Management",
                "dedicated_theme_section_description": "<p>Establish module for managing equipment, facilities, vehicles and other assets based on industry standards. Keep track of individual assets and schedule, assign and record maintenance work.</p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": "Maintenance Management",
                        "description": "Efficient preventive and corrective maintenance scheduling helps extend asset life, minimize downtime, and reduce operational disruptions."
                    },
                    "2": {
                        "title": "Depreciation Tracking",
                        "description": "Accurately calculate asset depreciation, ensuring financial transparency and compliance with accounting standards."
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Market-Specific Benefits",
                "dedicated_theme_section_description": "<p>The Fix Equipment Module caters to a wide range of industries, including manufacturing, healthcare, transportation, and more. Each sector can benefit from module-specific features tailored to their unique asset management needs.</p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Equipment Categories",
                "dedicated_theme_section_description": "<p>Equipment categories are the foundation of a well-organized equipment management system. They provide a structured approach to classifying assets based on their type, purpose, and characteristics.</p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            }
        ]';

        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"FixEquipment"},{"screenshots":"","screenshots_heading":"FixEquipment"},{"screenshots":"","screenshots_heading":"FixEquipment"},{"screenshots":"","screenshots_heading":"FixEquipment"},{"screenshots":"","screenshots_heading":"FixEquipment"}]';
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
            if (!MarketplacePageSetting::where('name', '=', $key)->where('module', '=', 'FixEquipment')->exists()) {
                MarketplacePageSetting::updateOrCreate(
                    [
                        'name' => $key,
                        'module' => 'FixEquipment'
                    ],
                    [
                        'value' => $value
                    ]
                );
            }
        }
    }
}
