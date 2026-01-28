<?php 

function WriteLog($text, $file)
{
      file_put_contents("../Logs/Modules.log" , file_get_contents("../Logs/Modules.log") . "\r\n" . $text);
}
	   $ik_shop_id = '577cf1823b1eafe96a8b4572'; // свой ид магазина, который указан в настройках магазина

        $err[0] = 'Ошибка - Проверка контрольной подписи данных о платеже провалена!';
        $err[1] = 'Ошибка - Неверная сумма платежа!';
        $err[2] = 'Ошибка - Shop ID!';
        $err[3] = 'FAIL';
        $ik_payment_amount = intval($_POST['ik_am']);

        $scrin = $_POST;
        unset($scrin["ik_sign"]);
        ksort($scrin, SORT_STRING); // сортируем по ключам в алфавитном порядке элементы массива 
        array_push($scrin, 'wEK83Pxv2S0EIPGi'); // добавляем в конец массива "секретный ключ"
        $signString = implode(':', $scrin); // конкатенируем значения через символ ":"
        $sign = base64_encode(md5($signString, true)); // берем MD5 хэш в бинарном виде по  сформированной строке и кодируем в BASE64
                 
        $date_now = date("Y-m-d H:i:s");


        if($_POST["ik_sign"] == $sign)
        {
            if($_POST['ik_co_id']  == $ik_shop_id)
            {
                if($scrin["ik_inv_st"] == 'success')
                {
                    mssql_connect("195.128.133.163", "sa", "mNwFcWFaJw2s");
                    mssql_select_db("MuOnline");
                    $sql = mssql_query("SELECT * FROM interkassa WHERE payment_id = '" . $_POST['ik_pm_no'] . "'");
                    $sqlArr = mssql_fetch_row($sql);
                    $acc = $sqlArr[0];
                    mssql_query("UPDATE CashShopData SET WCoinC = WCoinC + " . $ik_payment_amount . " WHERE AccountID = '" . $acc . "'" );
                    mssql_close();
                    //App::$DBs[$_SESSION['cabinet_db']]->insert('CashShopData', array('WCoinC' => 'WCoinC + ' . $ik_payment_amount ));
                    WriteLog("Module: [Payment], Account [{$acc}] Success" .':'.$date_now.': Count('.$ik_payment_amount.')', 'Modules.log');
                }
                else
                {
                    WriteLog("Module: [Payment], Account [{$acc}]" . $err[3].':'.$date_now.':', 'Modules.log');
                }
            }
            else
            {
                WriteLog("Module: [Payment], Account [{$acc}]" . $err[2].':'.$date_now.':', 'Modules.log');
            }
        
        }
        else
        {
            WriteLog("Module: [Payment], Account [{$acc}]" . $err[0].':'.$date_now.':', 'Modules.log');
        }
        echo 'ok';

