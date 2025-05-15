<?php

namespace Workdo\Planning\Database\Seeders;

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
        $module = 'Planning';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Planning';
        $data['product_main_description'] = '<p>Our Planning Module streamlines project management with comprehensive Challenge and Charter functionalities. Challenges provide a structured framework for project goals and timelines, while Charters define the specific roles, responsibilities, and tasks. The system ensures seamless collaboration and efficient tracking of project progress. With our setup, managing complex projects becomes intuitive and effective.
        </p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Effortless Management of Challenges and Charters';
        $data['dedicated_theme_description'] = '<p>The Planning Module of Dash SaaS offers a centralized hub for efficiently managing challenges and charters. Gone are the days of juggling multiple documents and struggling to keep track of progress. With the Planning Module, you can easily create, update, and monitor your challenges and charters in one intuitive platform. From defining objectives to tracking milestones, our module empowers you to stay organized and focused on achieving your goals.
        </p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Dynamic System Setup",
                "dedicated_theme_section_description": "<p>Customize your planning environment to align with your unique needs using the Planning Module of Dash SaaS system setup page. Easily add new categories, stages, and statuses to tailor the module to your specific workflow. Whether you are implementing industry standards or internal processes, our flexible setup ensures that the Planning Module adapts seamlessly to your requirements. Take control of your planning infrastructure and optimize efficiency with a system that evolves alongside your business.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Visualize Progress and Trends",
                "dedicated_theme_section_description": "<p>Gain valuable insights into your projects with the Planning Module of Dash SaaS dynamic charting tools. Track progress, identify trends, and make informed decisions using visual representations of your data. Whether you prefer Gantt charts, milestone timelines, or custom visualizations, our module provides the flexibility you need to effectively monitor and manage your projects. With the Planning Module, you can visualize your path to success and drive performance with confidence.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Seamless Collaboration",
                "dedicated_theme_section_description": "<p>Foster collaboration and communication among team members with the Planning Module of Dash SaaS collaborative features. Share updates, assign tasks, and streamline communication to keep everyone aligned and informed. Whether your team is co-located or distributed across multiple locations, our module ensures that everyone has access to the information they need to succeed. Say goodbye to siloed communication and hello to enhanced collaboration with the Planning Module of Dash SaaS.<\/p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image":"",
                "dedicated_theme_section_heading":"Accessible Anytime, Anywhere",
                "dedicated_theme_section_description":"<p>Enjoy the flexibility of accessing your planning data from anywhere with the Planning Module of Dash SaaS cloud-based platform. Whether you are in the office, on the road, or working remotely, our module provides seamless access across devices. With the Planning Module, you can plan with confidence knowing that your data is securely stored and readily available whenever you need it. Experience the freedom of planning on your terms with the Planning Module of Dash SaaS.<\/p>",
                "dedicated_theme_section_cards":{
                    "1":{
                        "title":null,
                        "description":null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Planning"},{"screenshots":"","screenshots_heading":"Planning"},{"screenshots":"","screenshots_heading":"Planning"},{"screenshots":"","screenshots_heading":"Planning"},{"screenshots":"","screenshots_heading":"Planning"}]';
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
