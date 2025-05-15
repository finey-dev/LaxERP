<?php

namespace Workdo\MeetingHub\Database\Seeders;

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
        $module = 'MeetingHub';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'MeetingHub';
        $data['product_main_description'] = '<p>Welcome to the Dash SaaS Meeting Hub Module! Our platform empowers you to efficiently manage all aspects of your meetings, including minutes, lists, reports, and types. With intuitive features and seamless integration, you can enhance collaboration and productivity within your organization.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Seamless Meeting Management with Dash SaaS';
        $data['dedicated_theme_description'] = '<p>Effortlessly manages meeting minutes, lists, reports, and types with Dash SaaS Meeting Hub. Streamline collaboration, enhance productivity, and stay organized with our intuitive platform.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Centralized Meeting Lists Repository","dedicated_theme_section_description":"<p>Keep track of all your meetings in one centralized repository with Dash SaaS. From scheduling to follow-up, our platform enables you to maintain detailed records of meeting dates, times, attendees, agendas, and associated documents for easy reference and tracking.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Efficient Meeting Minutes Management","dedicated_theme_section_description":"<p>Record and manage meeting minutes effortlessly with Dash SaaS. Our platform ensures that all discussions, decisions, and action items are documented accurately, providing a reliable reference point for future meetings and actions.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Insightful Meeting Reports Generation","dedicated_theme_section_description":"<p>Generate detailed meeting reports with Dash SaaS to summarize discussions, decisions, and action items. Our platform provides valuable insights for stakeholders, enabling informed decision-making and efficient follow-up on tasks and outcomes.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Customizable Meeting Types Classification","dedicated_theme_section_description":"<p>Classify meetings based on their purpose or format using customizable meeting types in Dash SaaS. Whether it is for project updates, brainstorming sessions, or client meetings, our platform allows you to categorize meetings for better organization and management.</p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"MeetingHub"},{"screenshots":"","screenshots_heading":"MeetingHub"},{"screenshots":"","screenshots_heading":"MeetingHub"},{"screenshots":"","screenshots_heading":"MeetingHub"},{"screenshots":"","screenshots_heading":"MeetingHub"}]';
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
