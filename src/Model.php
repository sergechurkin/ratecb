<?php

namespace ratecb;

use ratecb\Controller;
/* **ext** */
use sergechurkin\cform\cForm;
 
class Model {
    
    function getSoap() {
        ini_set("soap.wsdl_cache_enabled", "0");
        /*
         * Подключение к WEB сервису http://www.cbr.ru/scripts/Root.asp?PrtId=DWS
         */
        try {
            $soap = new \SoapClient("http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL", array("trace" => 1, "exceptions" => 0));
            return $soap;
        } catch (Exception $e) {
            throw new \RuntimeException($e->getMessage() . "<br>" . $e->getTraceAsString());
        }
    }
    function validateDate($date, $format) {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function createPage() {
        $action = (string)filter_input(INPUT_POST, 'action');
        $soap = $this->getSoap();
        if (!empty((string)filter_input(INPUT_POST, 'datetime'))) {
            $datetime = (string)filter_input(INPUT_POST, 'datetime');
            $checkbox = (string)filter_input(INPUT_POST, 'checkbox');
        } else {
            $datetime = $soap->GetLatestDateTime()->GetLatestDateTimeResult; // получаем последнюю дату, за которую есть курс 
            $checkbox = 'checked';
        }
        $cform = new cform();
        $title = 'Курсы валют ЦБ';
        $cform->bldHeader($title);
        $fieldsForm = [];
        $fieldsForm[] = ['Дата и время:', 2, 'datetime', 3, 'datetime', str_replace('T', ' ', $datetime), '', ''];
        $fieldsForm[] = ['Только USD & EUR:', 3, 'checkbox', 1, 'checkbox', $checkbox, '', ''];
        $buttons[] = ['Получить', 'submit', 'btn btn-success', ''];
        if ($action == 'validate' && !$this->validateDate(str_replace('T', ' ', $datetime), 'Y-m-d H:i:s')) {
            $fieldsForm[0][6] = 'Дата и время введены некорруктно \'Y-m-d H:i:s\'';
            $cform->bldForm($title, 'Ввод даты и времени', 10, $fieldsForm, $buttons, true);
        } else {
            $cform->bldForm($title, 'Ввод даты и времени', 10, $fieldsForm, $buttons, true);
            $fieldsBrw = [];
            $fieldsBrw[] = ['Название валюты', 'Vname',];
            $fieldsBrw[] = ['Номинал', 'Vnom',];
            $fieldsBrw[] = ['Курс', 'Vcurs',];
            $fieldsBrw[] = ['ISO Цифровой код', 'Vcode',];
            $fieldsBrw[] = ['ISO Символьный код', 'VchCode',];
            try {
                $retval = $soap->GetCursOnDate(array('On_date' => str_replace(' ', 'T', $datetime)));
            } catch (Exception $e) {
                echo "Error GetCursOnDate:<br>" . $e->getMessage() . "<br>" . $e->getTraceAsString();
            }
            $dom_xml = new \DomDocument();
            $dom_xml->loadXML($soap->__getLastResponse());
            $i = 0;
            foreach($dom_xml->getElementsByTagName('ValuteCursOnDate') as $event) {
                if ($checkbox !== 'checked' || 
                    $event->getElementsByTagName("Vcode")->item(0)->nodeValue == '840' ||    
                    $event->getElementsByTagName("Vcode")->item(0)->nodeValue == '978') {
                    $i++;
                    $r[] = [$event -> getElementsByTagName("Vname")->item(0)->nodeValue,
                            $event->getElementsByTagName("Vnom")->item(0)->nodeValue,
                            $event->getElementsByTagName("Vcurs")->item(0)->nodeValue,
                            $event->getElementsByTagName("Vcode")->item(0)->nodeValue,
                            $event->getElementsByTagName("VchCode")->item(0)->nodeValue,
                    ];
                }
            }
            $cform->bldTable('', 'Таблица курсов', 10, $fieldsBrw, $r, [], $i, 1000, 1, 0, 1, $i);
        }
    }    
}
