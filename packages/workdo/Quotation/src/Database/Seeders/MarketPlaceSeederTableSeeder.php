<?php

namespace Workdo\Quotation\Database\Seeders;

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
        $module = 'Quotation';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Quotation';
        $data['product_main_description'] = '<p>Quotation management is critical to any business, influencing customer perception, revenue generation, and overall efficiency. With Dash SaaS, you can revolutionize your quotation process, simplifying the creation, tracking, and conversion of quotes into invoices. Our comprehensive quotation module offers a range of features designed to streamline your workflow and enhance productivity.
        </p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Efficient Quotation Management';
        $data['dedicated_theme_description'] = '<p>Managing multiple quotes can be challenging without the right tools. Dash SaaS offers robust quotation management capabilities, allowing you to organize and track quotes effortlessly. You can categorize quotes by account type, customer, or warehouse, making it easy to locate and reference specific quotes as needed.</p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Simplified Quotation Creation",
                "dedicated_theme_section_description": "<p>Dash SaaS provides a user-friendly interface that makes creating quotations a breeze. You can easily input customer details, select products or services, and set pricing options with just a few clicks. Our intuitive platform eliminates manual calculations and ensures accuracy in every quote. Whether dealing with a single item or complex project pricing, Dash SaaS adapts to your needs, empowering you to generate professional-looking quotes quickly and efficiently.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Seamless Conversion to Invoices",
                "dedicated_theme_section_description": "<p>Once a quote is accepted, Dash SaaS enables seamless conversion to invoices with just a few clicks. You can automatically populate invoice details from the corresponding quote, saving time and reducing the risk of errors. Our platform supports various billing types and tax configurations, ensuring compliance with regional requirements. By streamlining the quotation-to-invoice process, Dash SaaS helps you maintain consistency and professionalism in your financial transactions.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Conversion to Point of Sale (POS)",
                "dedicated_theme_section_description": "<p>One of the unique features of Dash SaaS is the ability to convert quotations directly into point-of-sale (POS) transactions. Easily transform accepted quotes into POS orders, streamlining the sales process and enhancing customer service. By integrating quotation and POS functionalities, businesses can optimize their operations and drive revenue growth effectively. Experience the convenience of managing sales seamlessly from quotation to point of sale with Dash SaaS.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image":"",
                "dedicated_theme_section_heading":"Enhance Business Efficiency",
                "dedicated_theme_section_description":"<p>By leveraging Dash SaaS for quotation management, businesses can enhance efficiency across the board. With fewer manual tasks and streamlined workflows, you will free up valuable time and resources to focus on core business activities. Moreover, our data-driven insights provide valuable analytics on quoting trends, customer preferences, and sales performance, enabling informed decision-making and continuous improvement. With Dash SaaS, you can elevate your quotation process to new heights and drive business success.<\/p>",
                "dedicated_theme_section_cards":{
                    "1":{
                        "title":null,
                        "description":null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Quotation"},{"screenshots":"","screenshots_heading":"Quotation"},{"screenshots":"","screenshots_heading":"Quotation"},{"screenshots":"","screenshots_heading":"Quotation"},{"screenshots":"","screenshots_heading":"Quotation"}]';
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
