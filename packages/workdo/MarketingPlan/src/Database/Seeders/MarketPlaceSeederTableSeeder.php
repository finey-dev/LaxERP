<?php

namespace Workdo\MarketingPlan\Database\Seeders;

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
        $module = 'MarketingPlan';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'MarketingPlan';
        $data['product_main_description'] = '<p>Integrating marketing plan functionality into Dash SaaS represents a significant advancement in campaign management, enabling businesses to orchestrate, execute, and analyze marketing initiatives seamlessly within a single platform. By centralizing key marketing processes, Dash SaaS empowers teams to optimize their strategies, streamline workflows, and drive greater ROI from their marketing efforts.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Marketing Plan Module';
        $data['dedicated_theme_description'] = '<p>The Strategic Marketing Plan Module of Dash SaaS empowers businesses to design, implement, and monitor comprehensive marketing strategies tailored to their unique goals. This module provides tools for market analysis, target audience segmentation, campaign creation, and performance tracking.</p>';

        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"uploads\/marketplace_image\/BusinessPlan\/1692847416-dedicated_theme_section_image.png","dedicated_theme_section_status":"on","dedicated_theme_section_heading":"Efficient Campaign Management","dedicated_theme_section_description":"<p>Dash SaaS offers robust campaign management tools that allow businesses to plan, execute, and track marketing campaigns with ease. By integrating marketing plan functionality, Dash SaaS streamlines the entire campaign lifecycle, from ideation and scheduling to implementation and analysis. With features such as customizable campaign templates, automated scheduling, and real-time reporting, teams can collaborate more effectively and ensure campaigns are executed on time and within budget.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},

        {"dedicated_theme_section_image":"uploads\/marketplace_image\/BusinessPlan\/1692847446-dedicated_theme_section_image.png","dedicated_theme_section_status":"on","dedicated_theme_section_heading":"Targeted Audience Segmentation","dedicated_theme_section_description":"<p>Effective marketing hinges on understanding and reaching the right audience with the right message. Dash SaaS integrates sophisticated audience segmentation capabilities into its marketing plan functionality, allowing businesses to identify and target specific customer segments based on demographics, behavior, and preferences. By leveraging advanced targeting features, such as dynamic audience lists and personalized messaging, businesses can maximize the impact of their marketing efforts and drive higher engagement and conversions.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},

        {"dedicated_theme_section_image":"uploads\/marketplace_image\/BusinessPlan\/1692847446-dedicated_theme_section_image.png","dedicated_theme_section_status":"on","dedicated_theme_section_heading":"Comprehensive Analytics and Reporting","dedicated_theme_section_description":"<p>Dash SaaS provides comprehensive analytics and reporting tools that enable businesses to measure the performance of their marketing campaigns accurately. By integrating marketing plan functionality, Dash SaaS consolidates campaign data and metrics into intuitive dashboards, providing actionable insights into campaign effectiveness, audience engagement, and ROI. With real-time analytics and customizable reporting, teams can quickly assess performance, identify areas for improvement, and make data-driven decisions to optimize future campaigns.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},

        {"dedicated_theme_section_image":"uploads\/marketplace_image\/BusinessPlan\/1692847446-dedicated_theme_section_image.png","dedicated_theme_section_status":"on","dedicated_theme_section_heading":"Enhanced Collaboration and Alignment","dedicated_theme_section_description":"<p>Successful marketing requires collaboration and alignment across cross-functional teams. Dash SaaS facilitates collaboration by integrating marketing plan functionality with project management and communication tools, enabling teams to coordinate tasks, share resources, and communicate effectively throughout the campaign lifecycle. By centralizing communication and workflow management, Dash SaaS fosters greater alignment between marketing, sales, and other departments, ensuring everyone is working towards common goals and objectives.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';


        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"MarketingPlan"},{"screenshots":"","screenshots_heading":"MarketingPlan"},{"screenshots":"","screenshots_heading":"MarketingPlan"},{"screenshots":"","screenshots_heading":"MarketingPlan"},{"screenshots":"","screenshots_heading":"MarketingPlan"}]';
        $data['addon_heading'] = 'Why choose dedicated modules for Your Business?';
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
