<?php

namespace Workdo\BusinessProcessMapping\Database\Seeders;

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
        $module = 'BusinessProcessMapping';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Business Process Mapping';
        $data['product_main_description'] = "<p>The Business Process Mapping module encourages collaboration among team members, fostering a more cohesive and efficient work environment. With features for sharing and commenting on process diagrams, teams can collaborate in real time, ensuring that everyone is on the same page. This collaborative approach facilitates cross-functional communication and alignment, leading to smoother workflow execution and better outcomes for the organization.</p>";
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = '<h2>Improving Decision-Making:</h2>';
        $data['dedicated_theme_description'] = '<p>By providing a clear visual representation of business processes, the module facilitates better decision-making at all levels of the organization. Managers can easily identify areas where processes can be optimized or streamlined, allowing for more informed decision-making. Additionally, access to real-time process data and analytics enables stakeholders to make data-driven decisions, leading to improved overall performance and results.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Ensuring Compliance:","dedicated_theme_section_description":"<p>For businesses operating in regulated industries, compliance with industry standards and regulations is crucial. The Business Process Mapping module helps ensure compliance by providing a centralized platform for documenting and tracking processes. By mapping out regulatory requirements and incorporating them into process diagrams, businesses can easily demonstrate compliance during audits and inspections, reducing the risk of non-compliance and associated penalties.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Enhancing Customer Experience:","dedicated_theme_section_description":"<p>Optimizing business processes directly impacts the customer experience, leading to higher satisfaction and loyalty. With the Business Process Mapping module, businesses can identify and eliminate bottlenecks in customer-facing processes, such as order fulfillment or support ticket resolution. By streamlining these processes, businesses can deliver faster response times and more personalized service, ultimately enhancing the overall customer experience and driving customer retention and loyalty.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"BusinessProcessMapping"},{"screenshots":"","screenshots_heading":"BusinessProcessMapping"},{"screenshots":"","screenshots_heading":"BusinessProcessMapping"},{"screenshots":"","screenshots_heading":"BusinessProcessMapping"},{"screenshots":"","screenshots_heading":"BusinessProcessMapping"}]';
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
