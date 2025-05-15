<?php

namespace Workdo\FileSharing\Database\Seeders;

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
        $module = 'FileSharing';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'FileSharing';
        $data['product_main_description'] = '<p>In a general sense, a "FileSharing" module could refer to a component or feature within a software application or system that facilitates the sharing of files between users or devices.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = 'FileSharing Management';
        $data['dedicated_theme_description'] = '<p>File sharing management refers to the process of monitoring and controlling the sharing of digital files between users or within an organisation. A file sharing management system helps to streamline collaboration, you will also get various options like password security, types like email and link.</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"What is the purpose of the FileSharing feature?","dedicated_theme_section_description":"<p>The purpose of the file sharing feature is to facilitate the exchange and distribution of digital files between users or groups of users. It enables seamless collaboration, efficient communication and easy access to shared content. In it you can also select multiple users, add image, pdf, txt files etc and you can also share files through mail or link.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null},"2":{"title":"null","description":null},"3":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"File activity management","dedicated_theme_section_description":" <p>In file activity management, you can know and view file name, IP address, date, country, device, OS etc. when a file is downloaded.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null},"2":{"title":null,"description":null},"3":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Verification: Enhancing Security in File Sharing","dedicated_theme_section_description":"<p>Verification adds an extra layer of security to the file-sharing process, ensuring that only authorized users can access shared content. This feature includes user identity confirmation. By implementing verification measures, businesses can safeguard sensitive files and maintain data integrity while granting access to the right individuals. It helps prevent unauthorized access and ensures compliance with organizational policies.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null},"2":{"title":"null","description":null},"3":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Trash: Recover and Manage Deleted Files","dedicated_theme_section_description":"<p>The Trash feature acts as a safeguard against accidental file deletions. When a user deletes a file, it is moved to the Trash folder instead of being permanently removed. This allows for easy recovery of mistakenly deleted files, reducing the risk of data loss. Files in the Trash can be reviewed, restored, or permanently deleted, giving users control over their data and maintaining operational efficiency.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null},"2":{"title":"null","description":null},"3":{"title":null,"description":null}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Filtering: Efficient File Organization Made Simple","dedicated_theme_section_description":"<p>Filtering streamlines file management by enabling users to quickly locate specific files based on various criteria. Users can sort files by name, date, type, or size, making it easier to organize and access content. Advanced filtering options can also include keyword searches, enhancing productivity and reducing time spent searching for files in large repositories.<\/p>","dedicated_theme_section_cards":{"1":{"title":null,"description":null},"2":{"title":"null","description":null},"3":{"title":null,"description":null}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"FileSharing"},{"screenshots":"","screenshots_heading":"FileSharing"},{"screenshots":"","screenshots_heading":"FileSharing"},{"screenshots":"","screenshots_heading":"FileSharing"},{"screenshots":"","screenshots_heading":"FileSharing"}]';
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
