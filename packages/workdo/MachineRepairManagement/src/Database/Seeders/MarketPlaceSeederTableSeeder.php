<?php

namespace Workdo\MachineRepairManagement\Database\Seeders;

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
        $module = 'MachineRepairManagement';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Machine Repair Management';
        $data['product_main_description'] = '<p>The Machine Repair Management Integration dashboard provides a comprehensive solution for overseeing machine repair processes efficiently. It offers a detailed overview of repair schedules, invoice details, and maintenance reports, facilitating streamlined management of machine maintenance.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Efficient Machine Repair Management with Dash SaaS';
        $data['dedicated_theme_description'] = '<p>Streamlining maintenance workflows, Machine Repair Management ensures timely repairs, enhances equipment reliability, and maximizes operational efficiency in industrial settings.</p>';
        $data['dedicated_theme_sections'] = '[
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Streamlined Workflow",
                                                    "dedicated_theme_section_description": "<p></p>",
                                                    "dedicated_theme_section_cards": {
                                                        "1": {
                                                            "title": "Machines Management Page",
                                                            "description": "The Machines page allows users to create and manage machines scheduled for repair, including key details such as Machine ID, Model, Serial Number, and Installation Date. Automated reminders can be set to prompt clients for scheduled maintenance via email notifications, ensuring timely servicing of machines."
                                                        },
                                                        "2": {
                                                        "title": "Efficient Repair Requests Processing",
                                                            "description": "On the Repair Requests page, incoming repair requests can be reviewed and processed efficiently. Users can approve or decline requests based on availability and priority, facilitating effective communication with clients and optimizing repair scheduling."
                                                        },
                                                        "3": {
                                                        "title": "Insightful Maintenance Reports",
                                                            "description": "Gain valuable insights into past repairs and monitor machine maintenance history with comprehensive maintenance reports. Tracking repair dates, outcomes, and associated notes enables informed decision-making and proactive maintenance strategies to optimize machine performance."
                                                        }
                                                        }
                                                },
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Efficient Machine Repair Management",
                                                    "dedicated_theme_section_description": "<p>Dash SaaS revolutionizes machine repair management by offering a comprehensive solution tailored to streamline every aspect of the repair process. From optimized scheduling and transparent billing to insightful maintenance reports, Dash SaaS empowers businesses to efficiently manage machine repairs, ensuring minimal downtime and maximum productivity. With user-friendly interfaces and robust features, Dash SaaS simplifies repair request processing, defect tracking, and maintenance documentation, facilitating seamless communication and enhanced workflow efficiency.</p>",
                                                    "dedicated_theme_section_cards": {
                                                    "1": {
                                                        "title": null,
                                                        "description": null
                                                    }
                                                    }
                                                },
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Transparent Billing and Financial Oversight",
                                                    "dedicated_theme_section_description": "<p>Access to detailed invoice information streamlines billing processes and ensures transparency in financial transactions related to machine repairs. Users can track and manage invoices effortlessly, promoting accountability and financial clarity within the repair management system.</p>",
                                                    "dedicated_theme_section_cards": {
                                                        "1": {
                                                            "title": "Maintenance Documentation",
                                                            "description": "Document defects identified during maintenance checks and track repair progress effectively with the Defects and Maintenance page. Users can generate invoices for repair services provided and access a comprehensive view of all invoices and their status for easy monitoring and management."
                                                        },
                                                        "2": {
                                                        "title": "Financial Transparency",
                                                            "description": "Overall, the Machine Repair Management Integration in Dash SaaS streamlines workflow processes enhances communication, improves maintenance practices, and ensures financial transparency. With this solution, businesses can optimize their repair processes for efficiency, compliance, and client satisfaction."
                                                        }
                                                        }
                                                }
                                            ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"MachineRepairManagement"},{"screenshots":"","screenshots_heading":"MachineRepairManagement"},{"screenshots":"","screenshots_heading":"MachineRepairManagement"},{"screenshots":"","screenshots_heading":"MachineRepairManagement"},{"screenshots":"","screenshots_heading":"MachineRepairManagement"}]';
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
