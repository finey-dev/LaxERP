<?php

namespace Workdo\SWOTAnalysisModel\Database\Seeders;

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
        $module = 'SWOTAnalysisModel';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'SWOTAnalysisModel';
        $data['product_main_description'] = '<p>The Dash SaaS SWOT Analysis Module empowers businesses with a structured framework to evaluate their Strengths, Weaknesses, Opportunities, and Threats. This powerful tool is designed to provide clear insights into your organizations internal and external factors, helping you make informed strategic decisions.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'User-Friendly Interface';
        $data['dedicated_theme_description'] = '<p>The SWOT Analysis Module features an intuitive interface that simplifies the process of conducting thorough analyses.</p>';
        $data['dedicated_theme_sections'] = '[
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Strength Identification",
                "dedicated_theme_section_description": "<p>Uncover and document your business key strengths with the SWOT Analysis Module. By systematically evaluating areas where your company excels, you can leverage these strengths to maintain a competitive edge. Whether its your innovative products, exceptional customer service, or robust supply chain, this module helps you highlight and maximize your core competencies.</p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Weakness Assessment",
                "dedicated_theme_section_description": "<p>Understanding and addressing weaknesses is crucial for growth. The SWOT Analysis Module allows you to identify internal areas that need improvement. By recognizing these vulnerabilities, whether they are resource limitations, operational inefficiencies, or skill gaps, you can develop targeted strategies to overcome them and bolster your business’s resilience.</p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image": "",
                "dedicated_theme_section_heading": "Opportunity Exploration",
                "dedicated_theme_section_description": "<p>Stay ahead of the curve by identifying potential opportunities in your market with the SWOT Analysis Module. Analyze emerging trends, market shifts, and unmet customer needs to find new avenues for growth and expansion. This proactive approach enables you to capitalize on favorable conditions and strategic openings, positioning your business for long-term success.</p>",
                "dedicated_theme_section_cards": {
                    "1": {
                        "title": null,
                        "description": null
                    }
                }
            },
            {
                "dedicated_theme_section_image":"",
                "dedicated_theme_section_heading":"Threat Analysis",
                "dedicated_theme_section_description":"<p>Prepare for potential challenges by thoroughly examining external threats with the SWOT Analysis Module. Evaluate factors such as competitive pressures, regulatory changes, and economic fluctuations that could impact your business. By understanding these threats, you can devise contingency plans and risk mitigation strategies to safeguard your operations.</p>",
                "dedicated_theme_section_cards":{
                    "1":{
                        "title":null,
                        "description":null
                    }
                }
            }
        ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"SWOTAnalysisModel"},{"screenshots":"","screenshots_heading":"SWOTAnalysisModel"},{"screenshots":"","screenshots_heading":"SWOTAnalysisModel"},{"screenshots":"","screenshots_heading":"SWOTAnalysisModel"},{"screenshots":"","screenshots_heading":"SWOTAnalysisModel"}]';
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
