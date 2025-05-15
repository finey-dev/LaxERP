<?php

namespace Workdo\Sales\Database\Seeders;

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
        $module = 'Sales';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'Sales';
        $data['product_main_description'] = '<p>Never lose a client to poor sales management ever again! Easily manage your Sales, Calls, Documents, Quotes, Meeting Schedules, Opportunities, and Orders from one intuitive dashboard.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'Sales Makes It Super Easy For You To Manage Your Sales';
        $data['dedicated_theme_description'] = '<p>You can also find the individual overview of each of these elements broken down in percentages with their respective status. Additionally, know the top due tasks, meeting schedule, and monthly calls on this dashboard.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"The Smartest Way To Manage Your Sales","dedicated_theme_section_description":"<p>Sales monitor your sales performance like a coach and let you know whether you are on track. Get real-time reports about every sales activity you make, make smarter decisions, and manage your business’s general sales more efficiently.<\/p>","dedicated_theme_section_cards":{"1":{"title":"Manage All Your Sales From One Place","description":"Manage every aspect of your business sales from one place. Get sales updates on a daily, weekly, or monthly basis. Monitor your sales and accounts from a single comprehensive dashboard."},"2":{"title":"Manage Your Sales Order Easily","description":"Get an overview of all your quotes, sales orders, and invoices. Know who ordered what and easily monitor order fulfillment. Get vital information like status, account, assigned user, created date, etc. in one single list or grid."},"3":{"title":"Manage Quotes, Sales Orders, Sales Invoices and call","description":"Manage the individual aspects of your business. Get the status, amount, assigned users, created date, and other important information in a single list. Easily print out sales orders, sales invoices, and add new items to existing orders. Effectively manage your calls and schedule meetings. Plan ahead for your activities and always stay prepared."}}}, {"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Modify Vital Sales Info With Ease","dedicated_theme_section_description":"<p>Access a wide range of easily customizable features in one place. Get an overview of your sales invoices, quotes, and sales order- all under one roof.<\/p>","dedicated_theme_section_cards":{"1":{"title":"Convert Quotes to Sales Order","description":"Modify and update your generated sales quotes with ease. Convert your quotes to sales orders, get accurate information, and add new orders and products without stress."},"2":{"title":"Manage Your Payments Easily","description":"Get paid for work done, fast. Integrate several payment options for diverse clients and make the payment process stress-free. Easily safeguard your clients’ payments by using Stripe, PayPal, Flutterwave, and more."},"3":{"title":"Sales Invoice Report Management","description":"In the sales invoice, you can view the monthly report and in the sales invoice. You can also view the assigned accountant status-wise."}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Sales Order Report Manage","dedicated_theme_section_description":"<p>Get tailored analytics and reports sent to you automatically. Get an in-depth analysis of sales orders, sales invoices, and quotes. Easily filter through these reports by setting various parameters. Get status, amount, assigned users, created date, and other important information in a single list. Easily print sales orders, and invoices and add new items to existing orders.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Enhancing Customer Relationships","dedicated_theme_section_description":"<p>Beyond just managing sales transactions, our system empowers you to build and nurture strong relationships with your clients. By keeping track of interactions, calls, and meetings, you can personalize your approach and provide tailored solutions to meet their needs. This customer-centric approach not only increases satisfaction but also fosters loyalty, reducing the likelihood of client churn.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Continuous Improvement and Adaptation","dedicated_theme_section_description":"<p>Our sales management platform is not static; its designed for continuous improvement and adaptation. We regularly update our features based on user feedback and industry trends, ensuring that you always have access to the latest tools and technologies to stay ahead of the competition. With our commitment to innovation, you can trust that your sales processes will evolve alongside your business needs, driving sustainable growth and success.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"Sales"},{"screenshots":"","screenshots_heading":"Sales"},{"screenshots":"","screenshots_heading":"Sales"},{"screenshots":"","screenshots_heading":"Sales"},{"screenshots":"","screenshots_heading":"Sales"},{"screenshots":"","screenshots_heading":"Sales"},{"screenshots":"","screenshots_heading":"Sales"}]';
        $data['addon_heading'] = 'Why choose dedicated modulesfor Your Business?';
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
