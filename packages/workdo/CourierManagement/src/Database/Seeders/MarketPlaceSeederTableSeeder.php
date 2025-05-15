<?php

namespace Workdo\CourierManagement\Database\Seeders;

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
        $module = 'CourierManagement';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'CourierManagement';
        $data['product_main_description'] = '<p>Experience seamless deliveries with the Courier Management System in Dash SaaS. This dedicated tool ensures efficient order processing, real-time parcel tracking, and provides strategic insights through robust reporting features. Elevate your courier management capabilities with this comprehensive system.
        </p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = '<h2>Efficient <b>Dispatch Solutions </b> The Essence of Courier Management</h2>';
        $data['dedicated_theme_description'] = '<p>Seamlessly orchestrating order fulfillment, route optimization, and real-time tracking, our system ensures a streamlined workflow, minimizing delays and maximizing customer satisfaction.</p>';
        $data['dedicated_theme_sections'] = '
        [
         {
             "dedicated_theme_section_image": "",
             "dedicated_theme_section_heading": "Efficient Order Processing",
             "dedicated_theme_section_description": "The Courier Management module in Dash SaaS is a dedicated tool designed to streamline and  optimize courier-related processes. Its core functionality lies in efficient order processing, allowing users to input and manage  orders with details like sender information, recipient details, and parcel specifications. This ensures a smooth and error-free  order processing chain, providing a solid foundation for effective courier management.",
             "dedicated_theme_section_cards": {
             "1": {
                 "title": null,
                 "description": null
                 }
             }
         },
         {
             "dedicated_theme_section_image": "",
             "dedicated_theme_section_heading": "Real-time Tracking and Monitoring",
             "dedicated_theme_section_description": "A standout feature of the module is its advanced tracking and monitoring capabilities,  offering real-time insights into the status of parcels from dispatch to delivery. This includes route optimization, estimated time  of arrival, and proof of delivery functionalities. By providing transparency to customers and enabling businesses to manage  deliveries effectively, the module enhances overall courier service operations.",
             "dedicated_theme_section_cards": {
             "1": {
                 "title": null,
                 "description": null
                 }
             }
         },
         {
             "dedicated_theme_section_image": "",
             "dedicated_theme_section_heading": "Actionable Insights with Reporting and Analytics",
             "dedicated_theme_section_description": "To empower businesses with actionable insights, the Courier Management module includes  robust reporting and analytics tools. Users can generate reports on delivery performance, courier efficiency, and customer  satisfaction. These analytics serve as a valuable resource for businesses to make informed decisions, identify areas for  improvement, and ultimately optimize their courier management processes. In summary, the Courier Management module in Dash SaaS is  a comprehensive solution that addresses key aspects of courier services, from order processing to real-time tracking and analytics.",
             "dedicated_theme_section_cards": {
             "1": {
                 "title": null,
                 "description": null
                }
             }
         },
         {
            "dedicated_theme_section_image": "",
            "dedicated_theme_section_heading": "Swift Courier Approvals",
            "dedicated_theme_section_description": "Upon creating a courier, companies are provided with the convenience of an approval system, affording them the ability to review and decide on each submission efficiently. This feature ensures streamlined operations, allowing for prompt action to either approve or reject couriers based on specific criteria or requirements. With this functionality, companies can maintain control over their courier management process, ensuring adherence to standards and enhancing overall operational efficiency.",
            "dedicated_theme_section_cards": {
            "1": {
                "title": null,
                "description": null
               }
            }
        }
        ]';


        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"CourierManagement"},{"screenshots":"","screenshots_heading":"CourierManagement"},{"screenshots":"","screenshots_heading":"CourierManagement"},{"screenshots":"","screenshots_heading":"CourierManagement"},{"screenshots":"","screenshots_heading":"CourierManagement"}]';
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
