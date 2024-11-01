<?php

namespace src\utils;

class WUP_Addons{

    /**
     * @param $service_id
     * @return mixed
     */
    public static function get_addon_by_service_id( $service_id ){
   
        $all_addons = self::get_addons();
        $key = array_search( $service_id, array_column( $all_addons, "service_code" ) );
        return $all_addons[$key];
    }

    /**
     * @param $service_id
     * @return mixed
     */
    public static function get_addon_by_service_id_type( $service_id, $addon_type ){
        $addon = self::get_addon_by_service_id( $service_id );

        if ( in_array( $addon_type, array_keys( $addon['addons'] ) ) ) {
            return $addon['addons'][$addon_type];
        }
    }

    /**
     *
     */
    public static function get_addons(){
        return [  
            [
               "service_code" => "ASNPR",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "BPBDD",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "BPBE09",
                "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
                "service_code" => "PNL336",
                "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNLNO336",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNL330",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNLNO330",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNL332",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNLNO332",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BLFULL",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BCSEDSTD",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BLSGD",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNL335",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNL334",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNL349",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNLNO349",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BPSP",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "BMNOPIPAR",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "BMNOPIPA",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "BPPD",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "PNL340",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNLNO340",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNL342",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "PNLNO342",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "SBTLFIRREX",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "SBTLFISY",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "KLGRP",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BCSI",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BCS",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BDI",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BPALL",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BPA",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BPHDAP",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BPHKAP",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BLP",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BHP",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BCF",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "SBTLDKSYS",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "BPOSG",
              "addons" => [
               "sms_notification" => [
                   "value" => "NOT",
                    "phonenumber_key" => "text3"
                 ]
              ]
            ],
            [
               "service_code" => "AEX",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "ASP2",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "APC",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "APCS",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "ASWP2",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "ASPO",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "ASWS2",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "ASWP",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "ASWS",
              "addons" => [
               "sms_notification" => [
                   "value" => "DLVNOT",
                    "phonenumber_key" => "misc",
                    "addon_key" => "misctype",
                    "addon_key_value" => "PHONE"
                 ]
              ]
            ],
            [
               "service_code" => "DPDNLHOME",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "DPDNO",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "DPDNLPSD",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "DPDEEPICKUP",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "DSVDMYPACK",
              "addons" => [
               "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "KKSTD",
              "addons" => [
               "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "MTDPLP",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "PAF",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "BREKD",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "PUA",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "PUE",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "PAG",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "P79",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "PBM",
              "addons" => [
               "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "PDK83",
              "addons" => [
               "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "PDK83R",
              "addons" => [
               "sms_notification" => "NOTPHONE"
              ]
            ],
            [
                "service_code" => "P19DK",
                "addons" => [
                "sms_notification" => "NOTSMS",
                    "letter_notification" => "NOTLTR"
              ]
            ],
            [
                "service_code" => "P19DKBP",
                "addons" => [
                "sms_notification" => "NOTSMS",
                    "letter_notification" => "NOTLTR"
              ]
            ],
            [
                "service_code" => "P19DKDPD",
                "addons" => [
                "sms_notification" => "NOTSMS"
              ]
            ],
            [
                "service_code" => "PDK17",
                "addons" => [
                    "sms_notification" => "NOTSMS"
                ]
            ],
            [
                "service_code" => "PDK17BP",
                "addons" => [
                    "sms_notification" => "NOTSMS"
                ]
            ],
            [
                "service_code" => "PDK17DPD",
                "addons" => [
                    "sms_notification" => "NOTSMS"
                ]
            ],
            [
                "service_code" => "P52DK",
                "addons" => [
                    "sms_notification" => "NOTPHONE"
                ]
            ],
            [
                "service_code" => "P52DKR",
                "addons" => [
                    "sms_notification" => "NOTPHONE"
                ]
            ],
            [
               "service_code" => "PDKEP",
              "addons" => [
               "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "PDK18BP",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "PDK18DPD",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "PDKLASTMILE",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "P19DKSLUO",
              "addons" => [
               "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "P57",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "P42",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "P83",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "P38CDR",
              "addons" => [
               "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "P19",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "P19FI",
              "addons" => [
               "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "P19FIDPD",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "P19DPD",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
                "service_code" => "P17",
                "addons" => [
                    "sms_notification" => [
                        "value" => "DLVNOT",
                        "phonenumber_key" => "text3",
                        "addon_key" => false,
                        "addon_key_value" => false
                    ]
                ]
            ],
            [
               "service_code" => "P17FI",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "P17FIDPD",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "P30",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "DTPGSG",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "P19NO",
              "addons" => [
               "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "DTPGHD",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "P72NO",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "DTPG52",
              "addons" => [
               "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "P52FI",
              "addons" => [
               "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "P52",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "P18",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "P31",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "P14",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "P15",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "P18DPD",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "P13CDR",
              "addons" => [
               "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "P27CDR",
              "addons" => [
               "sms_notification" => "NOTSMS",
                 "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "P59",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "P20",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "P57SL",
              "addons" => [
               "email_prenotification" => "PRENOT",
                 "sms_notification" => "NOTPHONE"
              ]
            ],
            [
               "service_code" => "PO2144",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "PO2102",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "PO2185SS",
              "addons" => [
               "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "PO2190SS",
              "addons" => [
               "letter_notification" => "NOTLTR"
              ]
            ],
            [
               "service_code" => "PPFITRRET",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "PPFITR",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "ROYALMAIL24T",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "UPSEXPDTD",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSEXPDTG",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSEXPDTP",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSEXPD",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSEXPG",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSEXPP",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSEXPPLUSD",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSEXPPLUSG",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSEXPPLUSP",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSSAVD",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSSAVG",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSSAVP",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSSTDD",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "UPSSTDP",
              "addons" => [
               "email_prenotification" => "PRENOT"
              ]
            ],
            [
               "service_code" => "XPRSF",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "XPRSPKT",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "XPRSPLT",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "HH72",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "HH32",
              "addons" => [
                "sms_notification" => "NOTSMS"
              ]
            ],
            [
               "service_code" => "HH83",
              "addons" => [
               "sms_notification" => "NOTSMS"
              ]
            ]
        ];
   }
}