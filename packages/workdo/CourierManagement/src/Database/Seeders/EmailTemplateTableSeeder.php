<?php

namespace Workdo\CourierManagement\Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmailTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $emailTemplate = [
            'New Courier',
            'Courier Request Accept',
            'Courier Request Reject',
        ];
        $defaultTemplate = [
            'New Courier' => [
                'subject' => 'New Courier',
                'variables' => '{
                    "App Url": "app_url",
                    "App Name": "app_name",
                    "Company Name": "company_name",
                    "Tracking Id": "tracking_id",
                    "Tracking URL": "tracking_url"
                  }',
                'lang' => [
                    'ar' => '<p>مرحبًا,&nbsp;<br />مرحبا بك في {app_name}</p>
                    <p><strong>معرف التتبع </strong>: {tracking_id}<br /><strong>عنوان URL للتتبع</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>شكرًا,<br />{app_name}</p>',
                    'da' => '<p>Hej,&nbsp;<br />Velkommen til {app_name}</p>
                    <p><strong>Sporings-id</strong>: {tracking_id}<br /><strong>Sporings-URL</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Tak,<br />{app_name}</p>',
                    'de' => '<p>Hallo,&nbsp;<br />Willkommen zu {app_name}</p>
                    <p><strong>Tracking ID </strong>: {tracking_id}<br /><strong>Tracking-URL</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Danke,<br />{app_name}</p>',
                    'en' => '<p>Hello,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>Tracking Id </strong>: {tracking_id}<br /><strong>Tracking URL</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Thanks,<br />{app_name}</p>',
                    'es' => '<p>Hola,&nbsp;<br />Bienvenido a {app_name}</p>
                    <p><strong>ID de rastreo </strong>: {tracking_id}<br /><strong>URL de seguimiento</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Gracias,<br />{app_name}</p>',
                    'fr' => '<p>Bonjour,&nbsp;<br />Bienvenue à {app_name}</p>
                    <p><strong>Identifiant de suivi</strong>: {tracking_id}<br /><strong>URL de suivi</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Merci,<br />{app_name}</p>',
                    'it' => '<p>Ciao,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>ID monitoraggio </strong>: {tracking_id}<br /><strong>URL di monitoraggio</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Grazie,<br />{app_name}</p>',
                    'ja' => '<p>こんにちは,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>トラッキングID </strong>: {tracking_id}<br /><strong>トラッキングURL</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>ありがとう,<br />{app_name}</p>',
                    'nl' => '<p>Hallo,&nbsp;<br />Welkom bij {app_name}</p>
                    <p><strong>Tracking-ID </strong>: {tracking_id}<br /><strong>Tracking-URL</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Bedankt,<br />{app_name}</p>',
                    'pl' => '<p>Cześć,&nbsp;<br />Witamy w {app_name}</p>
                    <p><strong>Identyfikator śledzenia </strong>: {tracking_id}<br /><strong>Adres URL śledzenia</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Dzięki,<br />{app_name}</p>',
                    'ru' => '<p>Привет,&nbsp;<br />Добро пожаловать в {app_name}</p>
                    <p><strong>Идентификатор для отслеживания</strong>: {tracking_id}<br /><strong>URL отслеживания</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Спасибо,<br />{app_name}</p>',
                    'pt' => '<p>Olá,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>ID de rastreamento </strong>: {tracking_id}<br /><strong>URL de rastreamento</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Obrigado,<br />{app_name}</p>',
                    'tr' => '<p>Merhaba,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>Takip Kimliği </strong>: {tracking_id}<br /><strong>İzleme URLsi</strong> : {tracking_url}</p>
                    <p>{app_url}</p>
                    <p>Teşekkürler,<br />{app_name}</p>',
                ],
            ],
            'Courier Request Accept' => [
                'subject' => 'Courier Request Accept',
                'variables' => '{
                    "App Url": "app_url",
                    "App Name": "app_name",
                    "Company Name": "company_name",
                    "Tracking Id": "tracking_id",
                    "Tracking URL": "tracking_url"
                  }',
                'lang' => [
                    'ar' => '<p>مرحبًا,&nbsp;<br />مرحبا بك في {app_name}</p>
                    <p>لقد تم قبول طلب البريد السريع الخاص بك. هنا معرف التتبع : <strong> {tracking_id} </strong></p><br/>
                    <p>يمكنك تتبع البريد السريع الخاص بك من هنا :  {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>شكرًا,<br />{app_name}</p>',

                    'da' => '<p>Hej,&nbsp;<br />Velkommen til {app_name}</p>
                    <p>Din kureranmodning er blevet accepteret. Her er sporings-id : <strong> {tracking_id} </strong></p><br/>
                    <p>Du kan spore din kurer herfra: {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Tak,<br />{app_name}</p>',

                    'de' => '<p>Hallo,&nbsp;<br />Willkommen zu {app_name}</p>
                    <p>Ihre Kurieranfrage wurde angenommen. Hier ist die Tracking-ID : <strong> {tracking_id} </strong></p><br/>
                    <p>Sie können Ihren Kurier von hier aus verfolgen : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Danke,<br />{app_name}</p>',

                    'en' => '<p>Hello,&nbsp;<br />Welcome to {app_name}</p>
                    <p>Your Courier Request Has Been Accepted. Here Is Tracking Id :  <strong> {tracking_id} </strong></p><br/>
                    <p>You Can Track Your Courier From Here : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Thanks,<br />{app_name}</p>',

                    'es' => '<p>Hola,&nbsp;<br />Bienvenido a {app_name}</p>
                    <p>Su solicitud de mensajería ha sido aceptada. Aquí está la identificación de seguimiento : <strong> {tracking_id} </strong></p><br/>
                    <p>Puede rastrear a su mensajero desde aquí : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Gracias,<br />{app_name}</p>',

                    'fr' => '<p>Bonjour,&nbsp;<br />Bienvenue à {app_name}</p>
                    <p>Votre demande de messagerie a été acceptée. Voici lidentifiant de suivi : <strong> {tracking_id} </strong></p><br/>
                    <p>Vous pouvez suivre votre courrier à partir dici : : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Merci,<br />{app_name}</p>',

                    'it' => '<p>Ciao,&nbsp;<br />Welcome to {app_name}</p>
                    <p>La tua richiesta al corriere è stata accettata. Ecco lID di monitoraggio : <strong> {tracking_id} </strong></p><br/>
                    <p>Puoi monitorare il tuo corriere da qui: : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Grazie,<br />{app_name}</p>',

                    'ja' => '<p>こんにちは,&nbsp;<br />Welcome to {app_name}</p>
                    <p>宅配便リクエストが受理されました。追跡IDはこちらです : <strong> {tracking_id} </strong></p><br/>
                    <p>ここから宅配便を追跡できます : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>ありがとう,<br />{app_name}</p>',

                    'nl' => '<p>Hallo,&nbsp;<br />Welkom bij {app_name}</p>
                    <p>Uw koeriersverzoek is geaccepteerd. Hier is de tracking-ID : <strong> {tracking_id} </strong></p><br/>
                    <p>U kunt uw koerier vanaf hier volgen : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Bedankt,<br />{app_name}</p>',

                    'pl' => '<p>Cześć,&nbsp;<br />Witamy w {app_name}</p>
                    <p>Twoja prośba o przesyłkę kurierską została zaakceptowana. Oto identyfikator śledzenia : <strong> {tracking_id} </strong> </p><br/>
                    <p>Tutaj możesz śledzić swojego kuriera : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Dzięki,<br />{app_name}</p>',

                    'ru' => '<p>Привет,&nbsp;<br />Добро пожаловать в {app_name}</p>
                    <p>Ваш запрос на курьерскую доставку принят. Вот идентификатор отслеживания : <strong> {tracking_id} </strong></p><br/>
                    <p>Вы можете отслеживать своего курьера здесь : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Спасибо,<br />{app_name}</p>',

                    'pt' => '<p>Olá,&nbsp;<br />Welcome to {app_name}</p>
                    <p>Sua solicitação de correio foi aceita. Aqui está o ID de rastreamento : <strong> {tracking_id} </strong></p><br/>
                    <p>You Can Track Your Courier From Here : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Obrigado,<br />{app_name}</p>',

                    'tr' => '<p>Merhaba,&nbsp;<br />Welcome to {app_name}</p>
                    <p>Kargo Talebiniz Kabul Edildi. İşte Takip Kimliği : <strong> {tracking_id} </strong></p><br/>
                    <p>Kargonuzu Buradan Takip Edebilirsiniz : {tracking_url} </p>
                    <p>{app_url}</p>
                    <p>Teşekkürler,<br />{app_name}</p>',
                ],
            ],
            'Courier Request Reject' => [
                'subject' => 'Courier Request Reject',
                'variables' => '{
                    "App Url": "app_url",
                    "App Name": "app_name",
                    "Company Name": "company_name",
                    "Package Name": "package_name"
                  }',
                'lang' => [
                    'ar' => '<p>مرحبًا,&nbsp;<br />مرحبا بك في {app_name}</p>
                    <p><strong>اسم الحزمة </strong>: {package_name}<br /></p><br/>
                    <p> لقد تم رفض هذا البريد السريع.</p><br/>
                    <p>{app_url}</p>
                    <p>شكرًا,<br />{app_name}</p>',

                    'da' => '<p>Hej,&nbsp;<br />Velkommen til {app_name}</p>
                    <p><strong>Pakkenavn </strong>: {package_name}<br /></p><br/>
                    <p>Denne kurer er blevet afvist.</p><br/>
                    <p>{app_url}</p>
                    <p>Tak,<br />{app_name}</p>',

                    'de' => '<p>Hallo,&nbsp;<br />Willkommen zu {app_name}</p>
                    <p><strong>Paketnamen </strong>: {package_name}<br /></p><br/>
                    <p>Dieser Kurier wurde abgelehnt.</p><br/>
                    <p>{app_url}</p>
                    <p>Danke,<br />{app_name}</p>',

                    'en' => '<p>Hello,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>Package Name </strong>: {package_name}<br /></p><br/>
                    <p>This Courier Has Been Rejected.</p><br/>
                    <p>{app_url}</p>
                    <p>Thanks,<br />{app_name}</p>',

                    'es' => '<p>Hola,&nbsp;<br />Bienvenido a {app_name}</p>
                    <p><strong>Nombre del paquete </strong>: {package_name}<br /></p><br/>
                    <p>Este mensajero ha sido rechazado.</p><br/>
                    <p>{app_url}</p>
                    <p>Gracias,<br />{app_name}</p>',

                    'fr' => '<p>Bonjour,&nbsp;<br />Bienvenue à {app_name}</p>
                    <p><strong>Nom du paquet </strong>: {package_name}<br /></p><br/>
                    <p>Ce courrier a été rejeté.</p><br/>
                    <p>{app_url}</p>
                    <p>Merci,<br />{app_name}</p>',

                    'it' => '<p>Ciao,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>Nome del pacchetto </strong>: {package_name}<br /></p><br/>
                    <p>Questo corriere è stato rifiutato.</p><br/>
                    <p>{app_url}</p>
                    <p>Grazie,<br />{app_name}</p>',

                    'ja' => '<p>こんにちは,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>パッケージ名 </strong>: {package_name}<br /></p><br/>
                    <p>この宅配便は拒否されました.</p><br/>
                    <p>{app_url}</p>
                    <p>ありがとう,<br />{app_name}</p>',

                    'nl' => '<p>Hallo,&nbsp;<br />Welkom bij {app_name}</p>
                    <p><strong>Verpakkingsnaam </strong>: {package_name}<br /></p><br/>
                    <p>Deze koerier is afgewezen.</p><br/>
                    <p>{app_url}</p>
                    <p>Bedankt,<br />{app_name}</p>',

                    'pl' => '<p>Cześć,&nbsp;<br />Witamy w {app_name}</p>
                    <p><strong>Nazwa pakietu </strong>: {package_name}<br /></p><br/>
                    <p>Ten kurier został odrzucony.</p><br/>
                    <p>{app_url}</p>
                    <p>Dzięki,<br />{app_name}</p>',

                    'ru' => '<p>Привет,&nbsp;<br />Добро пожаловать в {app_name}</p>
                    <p><strong>Имя пакета </strong>: {package_name}<br /></p><br/>
                    <p>Этот курьер был отклонен.</p><br/>
                    <p>{app_url}</p>
                    <p>Спасибо,<br />{app_name}</p>',

                    'pt' => '<p>Olá,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>Nome do pacote </strong>: {package_name}<br /></p><br/>
                    <p>Este correio foi rejeitado.</p><br/>
                    <p>{app_url}</p>
                    <p>Obrigado,<br />{app_name}</p>',

                    'tr' => '<p>Merhaba,&nbsp;<br />Welcome to {app_name}</p>
                    <p><strong>Paket ismi </strong>: {package_name}<br /></p><br/>
                    <p>Bu Kurye Reddedildi</p><br/>
                    <p>{app_url}</p>
                    <p>Teşekkürler,<br />{app_name}</p>',
                ]
            ]
            
        ];
        foreach ($emailTemplate as $eTemp) {
            $table = EmailTemplate::where('name', $eTemp)->where('module_name', 'CourierManagement')->exists();
            if (!$table) {
                $emailtemplate =  EmailTemplate::create(
                    [
                        'name' => $eTemp,
                        'from' => 'CourierManagement',
                        'module_name' => 'CourierManagement',
                        'created_by' => 1,
                        'workspace_id' => 0
                    ]
                );
                foreach ($defaultTemplate[$eTemp]['lang'] as $lang => $content) {
                    EmailTemplateLang::create(
                        [
                            'parent_id' => $emailtemplate->id,
                            'lang' => $lang,
                            'subject' => $defaultTemplate[$eTemp]['subject'],
                            'variables' => $defaultTemplate[$eTemp]['variables'],
                            'content' => $content,
                        ]
                    );
                }
            }
        }
    }
}
