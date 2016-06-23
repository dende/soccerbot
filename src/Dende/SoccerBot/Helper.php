<?php
namespace Dende\SoccerBot;

class Helper {
	public static function codeToEmoji($code) {
		$alpha3_to_alpha2 =  [
				'AND' => 'AD',
				'ARE' => 'AE',
				'AFG' => 'AF',
				'ATG' => 'AG',
				'AIA' => 'AI',
				'ALB' => 'AL',
				'ARM' => 'AM',
				'ANT' => 'AN',
				'AGO' => 'AO',
				'ATA' => 'AQ',
				'ARG' => 'AR',
				'ASM' => 'AS',
				'AUT' => 'AT',
				'AUS' => 'AU',
				'ABW' => 'AW',
				'ALA' => 'AX',
				'AZE' => 'AZ',
				'BIH' => 'BA',
				'BRB' => 'BB',
				'BGD' => 'BD',
				'BEL' => 'BE',
				'BFA' => 'BF',
				'BGR' => 'BG',
				'BHR' => 'BH',
				'BDI' => 'BI',
				'BEN' => 'BJ',
				'BMU' => 'BM',
				'BRN' => 'BN',
				'BOL' => 'BO',
				'BRA' => 'BR',
				'BHS' => 'BS',
				'BTN' => 'BT',
				'BVT' => 'BV',
				'BWA' => 'BW',
				'BLR' => 'BY',
				'BLZ' => 'BZ',
				'CAN' => 'CA',
				'CCK' => 'CC',
				'COD' => 'CD',
				'CAF' => 'CF',
				'COG' => 'CG',
				'CHE' => 'CH',
				'CIV' => 'CI',
				'COK' => 'CK',
				'CHL' => 'CL',
				'CMR' => 'CM',
				'CHN' => 'CN',
				'COL' => 'CO',
				'CRI' => 'CR',
				'SCG' => 'CS',
				'CUB' => 'CU',
				'CPV' => 'CV',
				'CXR' => 'CX',
				'CYP' => 'CY',
				'CZE' => 'CZ',
				'DEU' => 'DE',
				'GER' => 'DE',
				'DJI' => 'DJ',
				'DNK' => 'DK',
				'DMA' => 'DM',
				'DOM' => 'DO',
				'DZA' => 'DZ',
				'ECU' => 'EC',
				'ENG' => 'GB',
				'NIR' => 'GB',
				'WAL' => 'GB',
				'EST' => 'EE',
				'EGY' => 'EG',
				'ESH' => 'EH',
				'ERI' => 'ER',
				'ESP' => 'ES',
				'ETH' => 'ET',
				'FIN' => 'FI',
				'FJI' => 'FJ',
				'FLK' => 'FK',
				'FSM' => 'FM',
				'FRO' => 'FO',
				'FRA' => 'FR',
				'GAB' => 'GA',
				'GBR' => 'GB',
				'GRD' => 'GD',
				'GEO' => 'GE',
				'GUF' => 'GF',
				'GHA' => 'GH',
				'GIB' => 'GI',
				'GRL' => 'GL',
				'GMB' => 'GM',
				'GIN' => 'GN',
				'GLP' => 'GP',
				'GNQ' => 'GQ',
				'GRC' => 'GR',
				'SGS' => 'GS',
				'GTM' => 'GT',
				'GUM' => 'GU',
				'GNB' => 'GW',
				'GUY' => 'GY',
				'HKG' => 'HK',
				'HMD' => 'HM',
				'HND' => 'HN',
				'HRV' => 'HR',
				'HTI' => 'HT',
				'HUN' => 'HU',
				'IDN' => 'ID',
				'IRL' => 'IE',
				'ISR' => 'IL',
				'IND' => 'IN',
				'IOT' => 'IO',
				'IRQ' => 'IQ',
				'IRN' => 'IR',
				'ISL' => 'IS',
				'ITA' => 'IT',
				'JAM' => 'JM',
				'JOR' => 'JO',
				'JPN' => 'JP',
				'KEN' => 'KE',
				'KGZ' => 'KG',
				'KHM' => 'KH',
				'KIR' => 'KI',
				'COM' => 'KM',
				'KNA' => 'KN',
				'PRK' => 'KP',
				'KOR' => 'KR',
				'KWT' => 'KW',
				'CYM' => 'KY',
				'KAZ' => 'KZ',
				'LAO' => 'LA',
				'LBN' => 'LB',
				'LCA' => 'LC',
				'LIE' => 'LI',
				'LKA' => 'LK',
				'LBR' => 'LR',
				'LSO' => 'LS',
				'LTU' => 'LT',
				'LUX' => 'LU',
				'LVA' => 'LV',
				'LBY' => 'LY',
				'MAR' => 'MA',
				'MCO' => 'MC',
				'MDA' => 'MD',
				'MDG' => 'MG',
				'MHL' => 'MH',
				'MKD' => 'MK',
				'MLI' => 'ML',
				'MMR' => 'MM',
				'MNG' => 'MN',
				'MAC' => 'MO',
				'MNP' => 'MP',
				'MTQ' => 'MQ',
				'MRT' => 'MR',
				'MSR' => 'MS',
				'MLT' => 'MT',
				'MUS' => 'MU',
				'MDV' => 'MV',
				'MWI' => 'MW',
				'MEX' => 'MX',
				'MYS' => 'MY',
				'MOZ' => 'MZ',
				'NAM' => 'NA',
				'NCL' => 'NC',
				'NER' => 'NE',
				'NFK' => 'NF',
				'NGA' => 'NG',
				'NIC' => 'NI',
				'NLD' => 'NL',
				'NOR' => 'NO',
				'NPL' => 'NP',
				'NRU' => 'NR',
				'NIU' => 'NU',
				'NZL' => 'NZ',
				'OMN' => 'OM',
				'PAN' => 'PA',
				'PER' => 'PE',
				'PYF' => 'PF',
				'PNG' => 'PG',
				'PHL' => 'PH',
				'PAK' => 'PK',
				'POL' => 'PL',
				'SPM' => 'PM',
				'PCN' => 'PN',
				'PRI' => 'PR',
				'PSE' => 'PS',
				'PRT' => 'PT',
				'PLW' => 'PW',
				'PRY' => 'PY',
				'QAT' => 'QA',
				'REU' => 'RE',
				'ROU' => 'RO',
				'RUS' => 'RU',
				'RWA' => 'RW',
				'SAU' => 'SA',
				'SLB' => 'SB',
				'SYC' => 'SC',
				'SDN' => 'SD',
				'SWE' => 'SE',
				'SGP' => 'SG',
				'SHN' => 'SH',
				'SVN' => 'SI',
				'SJM' => 'SJ',
				'SVK' => 'SK',
				'SLE' => 'SL',
				'SMR' => 'SM',
				'SEN' => 'SN',
				'SOM' => 'SO',
				'SUR' => 'SR',
				'STP' => 'ST',
				'SLV' => 'SV',
				'SYR' => 'SY',
				'SWZ' => 'SZ',
				'TCA' => 'TC',
				'TCD' => 'TD',
				'ATF' => 'TF',
				'TGO' => 'TG',
				'THA' => 'TH',
				'TJK' => 'TJ',
				'TKL' => 'TK',
				'TLS' => 'TL',
				'TKM' => 'TM',
				'TUN' => 'TN',
				'TON' => 'TO',
				'TUR' => 'TR',
				'TTO' => 'TT',
				'TUV' => 'TV',
				'TWN' => 'TW',
				'TZA' => 'TZ',
				'UKR' => 'UA',
				'UGA' => 'UG',
				'UMI' => 'UM',
				'USA' => 'US',
				'URY' => 'UY',
				'UZB' => 'UZ',
				'VAT' => 'VA',
				'VCT' => 'VC',
				'VEN' => 'VE',
				'VGB' => 'VG',
				'VIR' => 'VI',
				'VNM' => 'VN',
				'VUT' => 'VU',
				'WLF' => 'WF',
				'WSM' => 'WS',
				'YEM' => 'YE',
				'MYT' => 'YT',
				'ZAF' => 'ZA',
				'ZMB' => 'ZM',
				'ZWE' => 'ZW',
				'SUI' => 'CH',
			];
		if (strlen($code) == 3)
			$code = $alpha3_to_alpha2[$code];

		$code = strtoupper($code);
		$code = str_split($code);

		$emoji = "";
		$offset = 127397;
		foreach ($code as $c){
			$emoji .= Helper::utf8(ord($c)+$offset);
		}
		return $emoji;
	}

	public static function utf8($num)
	{
		if($num<=0x7F)       return chr($num);
		if($num<=0x7FF)      return chr(($num>>6)+192).chr(($num&63)+128);
		if($num<=0xFFFF)     return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
		if($num<=0x1FFFFF)   return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128).chr(($num&63)+128);
		return '';
	}

	public static function uniord($c)
	{
		$ord0 = ord($c{0}); if ($ord0>=0   && $ord0<=127) return $ord0;
		$ord1 = ord($c{1}); if ($ord0>=192 && $ord0<=223) return ($ord0-192)*64 + ($ord1-128);
		$ord2 = ord($c{2}); if ($ord0>=224 && $ord0<=239) return ($ord0-224)*4096 + ($ord1-128)*64 + ($ord2-128);
		$ord3 = ord($c{3}); if ($ord0>=240 && $ord0<=247) return ($ord0-240)*262144 + ($ord1-128)*4096 + ($ord2-128)*64 + ($ord3-128);
		return false;
	}

	public static function timeDifference(\DateTime $dt){
		$then = \Carbon\Carbon::instance($dt);
		$now = \Carbon\Carbon::now();
		$diffInMinutes = $now->diffInMinutes($then);
		$message = "";

		if ($diffInMinutes >= 180){
			//hours
			$message = "{$now->diffInHours($then)} Stunden";
		} else {
			//hours and minutes
			$nf = new \NumberFormatter("de-DE", \NumberFormatter::SPELLOUT);
			$diffInHours = $now->diffInHours($then);
			switch ($diffInHours){
				case 0:
					break;
				case 1:
					$message .= "einer Stunde";
					break;
				default:
					$message .= "{$nf->format($diffInHours)} Stunden";
					break;
			}

			$newDiffInMinutes = $now->diffInMinutes($then->copy()->subHours($diffInHours));

			switch ($newDiffInMinutes){
				case 0:

					break;
				case 1:
					if ($diffInHours > 0){
						$message .= " und einer Minute";
					} else {
						$message .= "einer	 Minute";
					}
					break;
				default:
					if ($diffInHours > 0){
						$message .= " und {$nf->format($newDiffInMinutes)} Minuten";
					} else {
						$message .= "{$nf->format($newDiffInMinutes)} Minuten";
					}
					break;
			}
		}
		return $message;
	}
}