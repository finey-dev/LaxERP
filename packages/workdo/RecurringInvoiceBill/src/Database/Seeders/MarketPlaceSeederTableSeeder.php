<?php

namespace Workdo\RecurringInvoiceBill\Database\Seeders;

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
        $module = 'RecurringInvoiceBill';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'RecurringInvoiceBill';
        $data['product_main_description'] = '<p>Managing recurring invoices/bills can be a cumbersome task for businesses, often requiring manual intervention and meticulous attention to detail. However, with the introduction of the Recurring Invoice/Bills Integration feature in Dash SaaS, businesses can now streamline their invoicing process effortlessly. By enabling this feature from the settings page, users gain access to a powerful tool that automates the creation and management of recurring invoices/bills, saving valuable time and resources.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Efficiency and Accuracy';
        $data['dedicated_theme_description'] = '<p>By automating the duplication of invoices/bills based on the specified recurrence period, Dash SaaS eliminates the risk of human error and ensures accuracy in billing.</p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Customization Options",
                "dedicated_theme_section_description": "<p>One of the key highlights of the Recurring Invoice/Bills Integration in Dash SaaS is its flexibility and customization options. Users have the freedom to tailor the recurrence period according to their specific requirements. With a simple click of a custom button, users can specify the interval between each recurrence, whether it\'s daily, weekly, monthly, or even set to recur indefinitely. This level of customization ensures that businesses can align their invoicing schedules with their unique billing cycles seamlessly.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Efficiency Elevated, Guaranteed",
                "dedicated_theme_section_description": "<p> In conclusion, the Recurring Invoice/Bills Integration feature in Dash SaaS revolutionizes the way businesses manage their invoicing processes. With its intuitive interface, customizable recurrence options, and automation capabilities, Dash SaaS empowers businesses to streamline their billing operations and focus on driving growth and success. Whether you\'re a small startup or a large enterprise, Dash SaaS provides the tools you need to simplify invoicing and enhance efficiency. Experience the convenience and efficiency of automated recurring invoices with Dash SaaS today.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"RecurringInvoiceBill"},{"screenshots":"","screenshots_heading":"RecurringInvoiceBill"},{"screenshots":"","screenshots_heading":"RecurringInvoiceBill"},{"screenshots":"","screenshots_heading":"RecurringInvoiceBill"},{"screenshots":"","screenshots_heading":"RecurringInvoiceBill"},{"screenshots":"","screenshots_heading":"RecurringInvoiceBill"}]';
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
