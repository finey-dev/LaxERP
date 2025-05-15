<?php

namespace Workdo\BudgetPlanner\Database\Seeders;

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
        $module = 'BudgetPlanner';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'BudgetPlanner';
        $data['product_main_description'] = '<p>In today`s fast-paced world, managing finances can often feel like navigating a labyrinth. However, with the Dash SaaS Budget Planner, financial clarity and control are within reach. This powerful tool empowers users to efficiently track income, manage expenses, and achieve their financial goals with ease. Whether you`re a seasoned investor or a budgeting novice, our platform provides the tools and insights needed to take charge of your financial future.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Budget Planner In Dash Saas';
        $data['dedicated_theme_description'] = '<p><Effortlessly manage income and expenses with Dash SaaS Budget Planner module.</p>';
        $data['dedicated_theme_sections'] = '[
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Effortless Income and Expense Tracking",
                                                    "dedicated_theme_section_description": "<p>Gone are the days of sifting through receipts and bank statements. With Dash SaaS, tracking income and expenses is as simple as a few clicks. Our intuitive interface allows users to categorize transactions, set budgets, and monitor spending in real time. Whether it`s a paycheck, freelance earnings, or everyday purchases, every financial transaction is effortlessly recorded and organized for easy reference.<\/p>",
                                                    "dedicated_theme_section_cards": {
                                                        "1": {
                                                            "title": null,
                                                            "description": null
                                                        }
                                                    }
                                                },
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Strategic Budgeting for Financial Success",
                                                    "dedicated_theme_section_description": "<p>Budgeting is the cornerstone of financial stability and success. With the Dash SaaS Budget Planner, users can create personalized budgets tailored to their unique financial goals. Whether you are saving for a down payment on a house, planning a dream vacation, or simply aiming to reduce debt, our platform provides the flexibility and tools needed to turn aspirations into realities.</p>",
                                                    "dedicated_theme_section_cards": {
                                                    "1": {
                                                        "title": null,
                                                        "description": null
                                                    }
                                                    }
                                                },
                                                {
                                                    "dedicated_theme_section_image": "",
                                                    "dedicated_theme_section_heading": "Insightful Reporting for Informed Decision-Making",
                                                    "dedicated_theme_section_description": "<p>Knowledge is power, especially when it comes to finances. Dash SaaS offers comprehensive reporting tools that provide insights into spending habits, income trends, and budget adherence. Visual representations of financial data make it easy to identify areas for improvement and make informed decisions about where to allocate resources. Whether you are looking to increase savings, reduce expenses, or invest for the future, our reporting features provide the clarity needed to take action.</p>",
                                                    "dedicated_theme_section_cards": {
                                                    "1": {
                                                        "title": null,
                                                        "description": null
                                                    }
                                                    }
                                                }
                                            ]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"BudgetPlanner"},{"screenshots":"","screenshots_heading":"BudgetPlanner"},{"screenshots":"","screenshots_heading":"BudgetPlanner"},{"screenshots":"","screenshots_heading":"BudgetPlanner"},{"screenshots":"","screenshots_heading":"BudgetPlanner"}]';
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
