<?php

namespace Workdo\Reminder\Database\Seeders;

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
        $module = 'Reminder';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Reminder';
        $data['product_main_description'] = '<p>To start receiving reminders, simply enable the reminder button from the system settings. Once activated, you\'ll receive timely alerts for all your scheduled reminders across your selected sources. This feature ensures that you never miss an important event or deadline, helping you stay productive and on top of your tasks.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Welcome to the Reminder Module';
        $data['dedicated_theme_description'] = '<p>The Reminder Module of Dash SaaS provides a simple yet powerful tool for managing your reminders. With this feature, you can create new reminders as needed and select from various sources through which you want to receive them.</p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Create Custom Reminders",
                "dedicated_theme_section_description": "<p>Creating new reminders is quick and easy. Simply input the details of your reminder, including the action, date, and any messages. Whether it\'s a lead, invoice, bill, deal, or user, our module allows you to set reminders for any event or activity effortlessly.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Select Reminder Sources",
                "dedicated_theme_section_description": "<p> Choose the sources through which you want to receive reminders. Whether it\'s WhatsApp, Slack, Twilio, or any other application, our module lets you select the platforms that are most convenient for you. This customization ensures that you receive reminders in the places you check most frequently, keeping you informed and organized.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Reminder"},{"screenshots":"","screenshots_heading":"Reminder"},{"screenshots":"","screenshots_heading":"Reminder"}]';
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
