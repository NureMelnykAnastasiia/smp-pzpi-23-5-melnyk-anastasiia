<?php
require_once "data.php";

function items(&$list, &$basket)
{
    $maxLen = 0;
    foreach ($list as $product) {
        preg_match_all('/./us', $product['title'], $chars);
        $maxLen = max($maxLen, count($chars[0]));
    }

    while (true) {
        echo "\n№  НАЗВА" . str_repeat(" ", $maxLen - 5 + 2) . "ЦІНА\n";
        foreach ($list as $id => $product) {
            preg_match_all('/./us', $product['title'], $chars);
            $padding = str_repeat(" ", $maxLen - count($chars[0]) + 2);
            printf("%-2d %s%s%d\n", $id, $product['title'], $padding, $product['cost']);
        }

        echo "   -----------\n";
        echo "0  ПОВЕРНУТИСЯ\n";
        echo "Виберіть товар: ";
        $input = trim(fgets(STDIN));

        if (!ctype_digit($input)) {
            echo "ПОМИЛКА! НЕОБХІДНО ВВЕСТИ ЧИСЛО\n\n";
            continue;
        }

        $selected = (int)$input;
        if ($selected === 0) break;

        if (!isset($list[$selected])) {
            echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n";
            continue;
        }

        $item = $list[$selected];
        echo "Вибрано: {$item['title']}\n";
        echo "Введіть кількість, штук: ";
        $amount = (int)trim(fgets(STDIN));

        if ($amount < 0 || $amount >= 100) {
            echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНУ КІЛЬКІСТЬ ТОВАРУ\n\n";
            continue;
        }

        if ($amount === 0) {
            unset($basket[$selected]);
            echo "ВИДАЛЯЮ З КОШИКА\n";
        } else {
            $basket[$selected] = $amount;
        }

        echo "У КОШИКУ:\nНАЗВА" . str_repeat(" ", $maxLen - 5 + 2) . "КІЛЬКІСТЬ\n";
        foreach ($basket as $k => $v) {
            $title = $list[$k]['title'];
            preg_match_all('/./us', $title, $chars);
            $pad = str_repeat(" ", $maxLen - count($chars[0]) + 2);
            echo $title . $pad . $v . "\n";
        }
        echo "\n";
    }
}

function printBill($list, $basket)
{
    if (empty($basket)) {
        echo "КОШИК ПОРОЖНІЙ\n";
        return;
    }

    $nameW = 5;
    $priceW = 4;
    $qtyW = 9;
    $sumW = 8;

    foreach ($basket as $k => $qty) {
        $title = $list[$k]['title'];
        $cost = $list[$k]['cost'];
        $total = $qty * $cost;

        preg_match_all('/./us', $title, $chars);
        $nameW = max($nameW, count($chars[0]));
        $priceW = max($priceW, strlen((string)$cost));
        $qtyW = max($qtyW, strlen((string)$qty));
        $sumW = max($sumW, strlen((string)$total));
    }

    echo "№  НАЗВА"
        . str_repeat(" ", $nameW - 5 + 2)
        . "ЦІНА" . str_repeat(" ", $priceW - 4 + 2)
        . "КІЛЬКІСТЬ" . str_repeat(" ", $qtyW - 9 + 2)
        . "ВАРТІСТЬ\n";

    $line = 1;
    $sumAll = 0;

    foreach ($basket as $k => $qty) {
        $title = $list[$k]['title'];
        $cost = $list[$k]['cost'];
        $subtotal = $qty * $cost;
        $sumAll += $subtotal;

        preg_match_all('/./us', $title, $chars);
        $namePad = str_repeat(" ", $nameW - count($chars[0]) + 2);
        $costPad = str_repeat(" ", $priceW - strlen((string)$cost) + 2);
        $qtyPad = str_repeat(" ", $qtyW - strlen((string)$qty) + 2);

        echo "$line  $title$namePad$cost$costPad$qty$qtyPad$subtotal\n";
        $line++;
    }

    echo "РАЗОМ ДО СПЛАТИ: $sumAll\n";
}

function user()
{
    while (true) {
        echo "Ваше імʼя: ";
        $username = trim(fgets(STDIN));
        $isValid = preg_match("/^[А-Яа-яЁёЇїІіЄєҐґA-Za-z'’\- ]+$/u", $username);
        $hasLetters = preg_match("/[А-Яа-яЁёЇїІіЄєҐґA-Za-z]/u", $username);
        if ($isValid && $hasLetters) break;
        echo "ПОМИЛКА! Імʼя повинно містити хоча б одну літеру та може містити лише літери, апостроф «'», дефіс «-», пробіл\n";
    }

    while (true) {
        echo "Ваш вік: ";
        $ageStr = trim(fgets(STDIN));
        if (!is_numeric($ageStr)) {
            echo "ПОМИЛКА! Вік має бути числом\n";
            continue;
        }
        $age = (int)$ageStr;
        if ($age >= 7 && $age <= 150) break;
        echo "ПОМИЛКА! Користувач не може бути молодшим 7-ми або старшим 150-ти років\n";
    }
    echo "\n";
}

$bucket = [];

while (true) {
    printMenu();
    $action = trim(fgets(STDIN));
    echo "\n";

    switch ($action) {
        case "1":
            items($itemsList, $bucket);
            break;
        case "2":
            printBill($itemsList, $bucket);
            break;
        case "3":
            user();
            break;
        case "0":
            exit;
        default:
            echo "ПОМИЛКА! Невідома команда. Спробуйте ще раз\n";
    }

    echo "\n";
}
