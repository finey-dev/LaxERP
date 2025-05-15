<?php

namespace Workdo\Reminder\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Workdo\Reminder\Entities\Reminder;
use Twilio\Rest\Client;
use Tzsk\Sms\Facades\Sms;
use Tzsk\Sms\Builder;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonEmailTemplate;


class ReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $superadmin = getAdminAllSetting();
        if(!empty($superadmin['reminder_notification_is']) && $superadmin['reminder_notification_is'] == 'on')
        {
            $reminders = Reminder::where('date',date('Y-m-d'))->get();
            foreach($reminders as $reminder){
                $company_settings = getCompanyAllSetting($reminder->created_by ,$reminder->workspace);
                $reminder_to = json_decode($reminder->to);
                $msg = strip_tags($reminder->message);
                $reminder_action = explode(',',$reminder->action);
                if(!empty($company_settings['reminder_notification_is']) && $company_settings['reminder_notification_is'] == 'on'){

                    foreach($reminder_action as $notification){
                            if($notification == "Twilio"){
                                $twilio_notification_is = isset($company_settings['twilio_notification_is']) ? $company_settings['twilio_notification_is'] : '';
                                $twilio_sid = isset($company_settings['twilio_sid']) ? $company_settings['twilio_sid'] : '';
                                $twilio_token = isset($company_settings['twilio_token']) ? $company_settings['twilio_token'] : '';
                                $twilio_from = isset($company_settings['twilio_from']) ? $company_settings['twilio_from'] : '';
                                $mobile_no = $reminder_to->twillo_mobile_no;
                                if (($twilio_notification_is == 'on') && (!empty($twilio_sid)) && (!empty($twilio_token)) && (!empty($twilio_from))) {
                                        try {
                                            $account_sid   = $twilio_sid;
                                            $auth_token    = $twilio_token;
                                            $twilio_number = $twilio_from;

                                            $client = new Client($account_sid, $auth_token);
                                            $client->messages->create($mobile_no, [
                                                'from' => $twilio_number,
                                                'body' => $msg,
                                            ]);
                                        } catch (\Exception $e) {
                                        }
                                    } else {
                                    }
                            }

                            if($notification == "Whatsapp"){
                                $twilio_notification_is = isset($company_settings['whatsapp_notification_is']) ? $company_settings['whatsapp_notification_is'] :'';
                                $whatsapp_twilio_sid = isset($company_settings['whatsapp_twilio_sid']) ? $company_settings['whatsapp_twilio_sid'] : '';
                                $twilio_token = isset($company_settings['whatsapp_twilio_auth_token']) ? $company_settings['whatsapp_twilio_auth_token'] : '';
                                $twilio_from = isset($company_settings['whatsapp_twilio_number']) ? $company_settings['whatsapp_twilio_number'] : '';
                                try
                                {
                                        $account_sid    = $whatsapp_twilio_sid;
                                        $auth_token = $twilio_token ;
                                        $twilio_number = $twilio_from;
                                        $mobile_no = $reminder_to->whatsapp_mobile_no;

                                        $twilio = new Client($account_sid, $auth_token);
                                        $message = $twilio->messages
                                        ->create("whatsapp:".$mobile_no, // to
                                            array(
                                            "from" => "whatsapp:".$twilio_number,
                                            "body" => $msg
                                            )
                                        );
                                    }
                                    catch(\Exception $e)
                                    {
                                    }

                            }
                            if($notification  == "Telegram"){
                                            try{
                                                // Set your Bot ID and Chat ID.
                                                $telegrambot = $reminder_to->telegram_access;
                                                $telegramchatid = $reminder_to->twillo_mobile_no;
                                                // Function call with your own text or variable
                                                $url = 'https://api.telegram.org/bot' . $telegrambot . '/sendMessage';
                                                $data = array(
                                                    'chat_id' => $telegramchatid,
                                                    'text' => $msg,
                                                );
                                                $options = array(
                                                    'http' => array(
                                                        'method' => 'POST',
                                                        'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                                                        'content' => http_build_query($data),
                                                    ),
                                                );

                                                $context = stream_context_create($options);
                                                $result = file_get_contents($url, false, $context);
                                                $url = $url;
                                            }
                                            catch(\Exception $e)
                                            {
                                            }
                                }
                                if($notification  == "Slack"){
                                    $slack_webhook = isset($reminder_to->slack_url) ? $reminder_to->slack_url : null;
                                    try{
                                            $ch = curl_init();
                                            curl_setopt($ch, CURLOPT_URL, $slack_webhook);
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt($ch, CURLOPT_POST, 1);
                                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['text' => $msg]));

                                            $headers = array();
                                            $headers[] = 'Content-Type: application/json';
                                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                            $result = curl_exec($ch);
                                            curl_close($ch);
                                    }
                                    catch(\Exception $e)
                                    {
                                    }
                                }
                                if($notification == "WhatsAppAPI"){
                                    $whatsappapi_notification_is = isset($company_settings['whatsappapi_notification_is']) ? $company_settings['whatsappapi_notification_is'] : '';
                                    $whatsapp_phone_number_id = isset($company_settings['whatsapp_phone_number_id']) ? $company_settings['whatsapp_phone_number_id'] : '';
                                    $whatsapp_access_token = isset($company_settings['whatsapp_access_token']) ? $company_settings['whatsapp_access_token'] : '';

                                    try{
                                        $mobile_no = isset($reminder_to->whatsappapi_mobile_no) ? $reminder_to->whatsappapi_mobile_no : null;
                                        $url = 'https://graph.facebook.com/v17.0/' . $whatsapp_phone_number_id . '/messages';

                                        $data = array(
                                            'messaging_product' => 'whatsapp',
                                            // 'recipient_type' => 'individual',
                                            'to' => $mobile_no,
                                            'type' => 'text',
                                            'text' => array(
                                                'preview_url' => false,
                                                'body' => $msg,
                                            ),
                                        );

                                        $headers = array(
                                            'Authorization: Bearer ' . $whatsapp_access_token,
                                            'Content-Type: application/json',
                                        );

                                        $ch = curl_init($url);

                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                                        $response = curl_exec($ch);
                                        $responseData = json_decode($response);

                                        curl_close($ch);
                                    }catch(\Exception $e)
                                    {
                                    }
                                }
                                if($notification == "SMS"){
                                    if(!empty($company_settings['sms_notification_is']) && $company_settings['sms_notification_is'] == "on"){
                                        $mobile_no = $reminder_to->sms_mobile_no;
                                        self::active_driver($reminder->created_by ,$reminder->workspace);
                                        try{
                                            $response = Sms::via($company_settings['sms_setting'])->send($msg, function($sms) use
                                            ($mobile_no) {
                                                $sms->to($mobile_no);
                                            });
                                        }catch(\Exception $e)
                                        {
                                        }
                                    }
                                }
                                if($notification == "Email"){
                                    $message = $reminder->message;
                                    $content = [
                                        'from' => !empty($company_settings['company_name']) ? $company_settings['company_name'] :env('APP_NAME'),
                                        'subject' => $reminder->module,
                                        'content'=> '<p>' .$msg.'</p>',
                                    ];
                                    $content = (object)$content;
                                    try
                                    {
                                        SetConfigEmail($reminder->created_by ,$reminder->workspace);
                                        $email = $reminder_to->email_address;
                                        $response = Mail::to($email)->send(new CommonEmailTemplate($content,$reminder->created_by,$reminder->workspace));
                                    }
                                    catch(\Exception $e)
                                    {
                                    }
                                }

                    }

                }
            }
        }
    }


    public static function active_driver($company_id = null, $workspace_id = null){
        $company_settings = getCompanyAllSetting($company_id ,$workspace_id);
            if($company_settings['sms_setting'] == "twilio" ){

                $twilio_sid = isset($company_settings['sms_twilio_sid']) ? $company_settings['sms_twilio_sid'] : '';
                $twilio_token = isset($company_settings['sms_twilio_token']) ? $company_settings['sms_twilio_token'] : '';
                $twilio_from = isset($company_settings['sms_twilo_from_number']) ? $company_settings['sms_twilo_from_number'] : '';
                config(
                    [
                        'sms.drivers.twilio.sid' => $twilio_sid,
                        'sms.drivers.twilio.token' => $twilio_token,
                        'sms.drivers.twilio.from' => $twilio_from,
                    ]
                );
            }elseif($company_settings['sms_setting'] == "sns"){
                $sns_access_key = isset($company_settings['sns_access_key']) ? $company_settings['sns_access_key'] : '';
                $sns_secret_key = isset($company_settings['sns_secret_key']) ? $company_settings['sns_secret_key'] : '';
                $sns_region = isset($company_settings['sns_region']) ? $company_settings['sns_region'] : '';
                $sns_sender_id = isset($company_settings['sns_sender_id']) ? $company_settings['sns_sender_id'] : '';
                $sns_type = isset($company_settings['sns_type']) ? $company_settings['sns_type'] : '';

                config(
                    [
                        'sms.drivers.sns.sid' => $sns_access_key,
                        'sms.drivers.sns.token' => $sns_secret_key,
                        'sms.drivers.sns.from' => $sns_region,
                        'sms.drivers.sns.from' => $sns_sender_id,
                        'sms.drivers.sns.from' => $sns_type,
                    ]
                );

            }elseif($company_settings['sms_setting'] == "clockwork"){
                $clockwork_api_key = isset($company_settings['clockwork_api_key']) ? $company_settings['clockwork_api_key'] : '';


                config(
                    [
                        'sms.drivers.clockwork.key' => $clockwork_api_key,

                    ]
                );
            }elseif($company_settings['sms_setting'] == "melipayamak"){
                $melipayamak_username = isset($company_settings['melipayamak_username']) ? $company_settings['melipayamak_username'] : '';
                $melipayamak_password = isset($company_settings['melipayamak_password']) ? $company_settings['melipayamak_password'] : '';
                $melipayamak_from_number = isset($company_settings['melipayamak_from_number']) ? $company_settings['melipayamak_from_number'] : '';
                config(
                    [
                        'sms.drivers.melipayamak.username' => $melipayamak_username,
                        'sms.drivers.melipayamak.password' => $melipayamak_password,
                        'sms.drivers.melipayamak.from' => $melipayamak_from_number,
                        'sms.drivers.melipayamak.flash' => false,

                    ]
                );
            }elseif($company_settings['sms_setting'] == "kavenegar"){
                $kavenegar_apiKey = isset($company_settings['kavenegar_apiKey']) ? $company_settings['kavenegar_apiKey'] : '';
                $kavenegar_from_number = isset($company_settings['kavenegar_from_number']) ? $company_settings['kavenegar_from_number'] : '';
                config(
                    [
                        'sms.drivers.kavenegar.apiKey' => $kavenegar_apiKey,
                        'sms.drivers.kavenegar.from' => $kavenegar_from_number,

                    ]
                );
            }else{
                $smsgatewayme_apiToken = isset($company_settings['smsgatewayme_apiToken']) ? $company_settings['smsgatewayme_apiToken'] : '';
                $Smsgatewayme_device_id = isset($company_settings['Smsgatewayme_device_id']) ? $company_settings['Smsgatewayme_device_id'] : '';
                config(
                    [
                        'sms.drivers.smsgatewayme.apiToken' => $smsgatewayme_apiToken,
                        'sms.drivers.smsgatewayme.from' => $Smsgatewayme_device_id,

                    ]
                );
            }


    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
