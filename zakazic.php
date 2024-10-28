<?php

function getAccessToken($client_id, $client_secret, $redirect_uri) {
    // Здесь вставьте полученный код авторизации
    $code = "def502006a3f7313e0c8ae69ed2826f5e228ae8c9165fee8c0de3936c390e6e9499802b1d8fba961ec0b66e5760bd4e06b5adae22b6593e69584c7f3f3ff64edc5a7e33ced37b5b60167f17f0398353568149ad4a059e417ad807aa0af20b0f1893f86829a774cb2c1cf2dcbcc0183f7736ab3af6b9d9c85af70e3597759c93aec7e9afcfd451c7624e0d02ba64af8c244d7342a493b227b5bc311d32d8a24261f6730b997e28a4c2cd36fd64c14c79aae11ebc4824766503208de34e128e8c64a2368209ef027707b9d1ff235e2d684b9b054fd3d505d95116c933d668202ef3646d50dda316ac10c044c6173c4ef70c6fb94cc8936330fe83dd837328a891c9f51841af4a9fabe8dda5267aaa366e53f7cf7782c88d7282da0de1d7ccec860999fb2f806a5ea20d36ee3ae0c600c377e4f4c30bdc58153800fa7fe68526b8906552c9201ecdad265be4a4bcaf7b03ec0a299ae07695d809228f98354ad509ee2cb1945b9a5e224a3c233cbabbb20740ff141d6cd1deaea5d8de0becefafcedc404313195ae1204bf57751fb9348faa99251c137b566c608c372245fc585fab392184d4583b12c5bcc296351c1c8d6096dadadfa8f43329a1aa404a7ba7feeee1c9f4a9e3a10d6187203be896d0d21d2c0e5e340c23c18ad6a18df17e530603"; 

    $auth_url = "https://dles13.amocrm.ru/oauth2/access_token";

    $data = [
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "grant_type" => 'authorization_code',
        "code" => $code,
        "redirect_uri" => $redirect_uri,
    ];

    $ch = curl_init($auth_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
	Print_r($response);
}

function createDeal($access_token, $domain, $deal_data) {
    $url = "https://$domain.amocrm.ru/api/v4/leads"; // URL для создания сделки

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($deal_data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
	Print_r($response);
}

function createContact($access_token, $domain, $contact_data) {
    $url = "https://$domain.amocrm.ru/api/v4/contacts"; // URL для создания контакта

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($contact_data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
	Print_r($response);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $price = $_POST['price'];

    // Получите токен доступа
    $client_id = "65558191-cebb-4506-92af-35d1fcf2376c"; // Ваш client_id
    $client_secret = "LhjWJsKitdUEkCCDQu8cYHOddiV6dfs4VYUCO1Sqq19cg0Fc2BdiuwvRnjJGuIVb"; // Ваш client_secret
    $redirect_uri = "https://u2234735.isp.regruhosting.ru/"; // Ваш redirect_uri
    $access_token = getAccessToken($client_id, $client_secret, $redirect_uri)['access_token'];
    $domain = "dles13"; // Ваш домен в AmoCRM

    // Создайте контакт
    $contact_data = [
        "name" => $name,
        "custom_fields_values" => [
            [
                "field_id" => 1, // Например, ID поля для телефона
                "values" => [[
                    "value" => $phone,
                    "enum" => "WORK" // Тип контакта (рабочий)
                ]]
            ],
            [
                "field_id" => 429401, // Например, ID поля для email
                "values" => [[
                    "value" => $email,
                    "enum" => "WORK"
                ]]
            ]
        ]
    ];

    $contact_response = createContact($access_token, $domain, $contact_data);
    $contact_id = $contact_response['id'];

    // Создайте сделку
    $deal_data = [
        "name" => "Сделка с " . $name,
        "price" => $price,
        "contacts_id" => [$contact_id] // Привязываем контакт к сделке
    ];
    
    $deal_response = createDeal($access_token, $domain, $deal_data);

    // Обработка ответа (например, вывод сообщений или редирект)
    if ($deal_response) {
        echo "Сделка успешно создана!";
    } else {
        echo "Ошибка при создании сделки!";
    }
}

?>