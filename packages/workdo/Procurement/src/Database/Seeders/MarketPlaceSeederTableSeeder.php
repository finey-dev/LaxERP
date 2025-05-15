<?php

namespace Workdo\Procurement\Database\Seeders;

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
        $module = 'Procurement';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Procurement';
        $data['product_main_description'] = '<p>Managing the procurement process can be a time-consuming and complex task.  Dash SaaS simplifies this process by providing a central hub for all your procurement needs.  Create purchase orders, manage vendor information, track spending, and gain real-time insights into your procurement activities – all within a user-friendly interface.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Comprehensive Procurement Oversight';
        $data['dedicated_theme_description'] = '<p>Dash SaaS ensures compliance and seamless integration with your existing systems, providing comprehensive records and enhanced transparency for streamlined procurement operations.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Boost Efficiency with Automated Workflows","dedicated_theme_section_description":"<p>Automate repetitive tasks and eliminate manual data entry with Dash SaaS`s built-in workflows. Standardize your procurement process by setting up approval chains for purchase orders and ensure all necessary steps are completed before finalizing transactions.  This not only saves time but also minimizes errors and improves overall efficiency.</p>"},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Gain Visibility and Control Over Spending","dedicated_theme_section_description":"<p>Dash SaaS provides detailed reporting and analytics to give you a clear picture of your spending habits. Track expenses by category, vendor, or department and identify areas for cost savings.  Gain valuable insights into your procurement trends and make informed decisions that optimize your budget allocation.</p>"},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Simplified Vendor Collaboration","dedicated_theme_section_description":"<p>Maintain strong relationships with your vendors through seamless communication within Dash SaaS.Share purchase orders electronically, receive updates on order status, and manage all communication with vendors in a centralized location.  This fosters transparency and collaboration throughout the procurement lifecycle.</p>"},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Streamlined Compliance and Audit Readiness","dedicated_theme_section_description":"<p>Ensure compliance with organizational policies and regulatory requirements using Dash SaaS`s robust procurement module. Maintain comprehensive records of all procurement activities, including approvals, transactions, and communications. This centralized record-keeping not only simplifies audit processes but also enhances transparency and accountability across your procurement operations..</p>"},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Flexible Integration with Existing Systems","dedicated_theme_section_description":"<p>Dash SaaS seamlessly integrates with your existing ERP and financial systems, allowing for smooth data flow and reducing the need for duplicate data entry. By connecting with your current tools, you can ensure consistency and accuracy across your procurement and financial data. This flexible integration supports a cohesive and streamlined procurement process, enhancing overall productivity and data integrity.</p>"}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Procurement"},{"screenshots":"","screenshots_heading":"Procurement"},{"screenshots":"","screenshots_heading":"Procurement"},{"screenshots":"","screenshots_heading":"Procurement"},{"screenshots":"","screenshots_heading":"Procurement"}]';
        $data['addon_heading'] = 'Why choose dedicated modules for Your Business?';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Why choose dedicated modules for Your Business?';
        $data['whychoose_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['pricing_plan_heading'] = 'Empower Your Workforce with DASH';
        $data['pricing_plan_description'] = '<p>Access over Premium Add-ons for Accounting, HR, Payments, Leads, Communication, Management, and more, all in one place!.</p>';
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
