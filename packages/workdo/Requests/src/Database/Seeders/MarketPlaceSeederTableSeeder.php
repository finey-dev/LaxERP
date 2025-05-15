<?php

namespace Workdo\Requests\Database\Seeders;

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
        $module = 'Requests';
        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Requests';
        $data['product_main_description'] = '<p>The Requests Module simplifies the process of making your forms accessible. Once you have designed your custom form using the user-friendly interface, just click Publish. The module will generate a shareable link or embed code, making it easy to integrate into your website or application. This ensures your form is instantly available to users, allowing you to gather important data, feedback, or inquiries effortlessly. The Requests Module streamlines the sharing and implementation of your forms, making it quick and straightforward.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Add Request module, using this provides create lead option';
        $data['dedicated_theme_description'] = '<p>Customizable to align with branding and design preferences, Request generates leads from various websites. It allows for the creation and management of essential forms with diverse fields, including Text, Email, Numeric, and Description fields.</p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Designing Your Request Forms.",
                "dedicated_theme_section_description": "<p>Creating fields in the Requests Module is intuitive and easy. Using the straightforward interface, you can choose from various field types, such as Text, Email, Number, Date, and more. You can then customize each field’s properties, including labels, placeholders, and validation rules, to match your specific needs. The Requests Module’s design ensures you can build customized forms with the exact fields required, all with minimal effort.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Efficient Request Management: Leveraging Powerful Actions",
                "dedicated_theme_section_description": "<p> In the Actions section of the platform, you all find numerous features designed to enhance your workflow. You can quickly copy the request link, edit or view form fields, review responses, and convert requests into leads or CRM entries with ease. When you transform a request into a lead, all details are seamlessly moved to the lead column. Alternatively, converting it into a CRM entry allows for straightforward form completion directly from the front end. Additionally, you have the option to rename the form to suit your preferences, providing added flexibility.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Requests"},{"screenshots":"","screenshots_heading":"Requests"},{"screenshots":"","screenshots_heading":"Requests"},{"screenshots":"","screenshots_heading":"Requests"},{"screenshots":"","screenshots_heading":"Requests"},{"screenshots":"","screenshots_heading":"Requests"},{"screenshots":"","screenshots_heading":"Requests"}]';
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
