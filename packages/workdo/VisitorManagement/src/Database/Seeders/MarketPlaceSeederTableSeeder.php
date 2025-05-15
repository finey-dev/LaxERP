<?php

namespace Workdo\VisitorManagement\Database\Seeders;

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
        $module = 'VisitorManagement';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Visitor Management';
        $data['product_main_description'] = '<p>Are you looking for an efficient, comprehensive, and reliable solution to manage visitor check-ins, check-outs, and activity tracking within your premises? Our Visitor Management System module is the perfect solution for your needs!</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Check-In/Check-Out';
        $data['dedicated_theme_description'] = '<p>Visitors can check in and out using this system, recording their entry and exit times along with the purpose of their visit.</p>';
        $data['dedicated_theme_sections'] = '[
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Visitor Tracking",
                                                    "dedicated_theme_section_description": "It enables the tracking of visitor movement within the premises, ensuring an accurate record of their activities.",
                                                    "dedicated_theme_section_cards": {
                                                    "1": {
                                                            "title": "Real-time Updates",
                                                            "description": "The system updates the timeline in real-time, reflecting changes as visitors check in or out."
                                                        }
                                                    },
                                                    "2": {
                                                            "title": "Security and Access Control",
                                                            "description": "Helps in managing security by providing insights into who is currently present on-site and ensuring only authorized individuals are allowed access."
                                                        }
                                                    },
                                                    "3": {
                                                            "title": "Analytics and Reporting",
                                                            "description": "The system can generate reports and analytics based on visitor data, helping in analyzing trends, peak visiting hours, or common purposes of visits."
                                                        }
                                                    }
                                                }
                                            ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Visitor Management"},{"screenshots":"","screenshots_heading":"Visitor Management"},{"screenshots":"","screenshots_heading":"Visitor Management"},{"screenshots":"","screenshots_heading":"Visitor Management"},{"screenshots":"","screenshots_heading":"Visitor Management"}]';
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
