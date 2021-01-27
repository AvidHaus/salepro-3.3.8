<?php

namespace NumberToWords\Legacy\Numbers\Words\Locale;

use NumberToWords\Exception\NumberToWordsException;
use NumberToWords\Legacy\Numbers\Words;

class Hu extends Words
{
    const LOCALE = 'hu';
    const LANGUAGE_NAME = 'Hungarian';
    const LANGUAGE_NAME_NATIVE = 'Magyar';

    private $minus = 'mínusz';

    private static $exponent = [
        0   => [''],
        3   => ['ezer'],
        6   => ['millió'],
        9   => ['milliárd'],
        12  => ['billió'],
        15  => ['billiárd'],
        18  => ['trillió'],
        21  => ['trilliárd'],
        24  => ['kvadrillió'],
        27  => ['kvadrilliárd'],
        30  => ['kvintillió'],
        33  => ['kvintilliárd'],
        36  => ['szextillió'],
        39  => ['szextilliárd'],
        42  => ['szeptillió'],
        45  => ['szeptilliárd'],
        48  => ['oktillió'],
        51  => ['oktilliárd'],
        54  => ['nonillió'],
        57  => ['nonilliárd'],
        60  => ['decillió'],
        63  => ['decilliárd'],
    ];

    private static $digits = [
        'nulla',
        'egy',
        'kettő',
        'három',
        'négy',
        'öt',
        'hat',
        'hét',
        'nyolc',
        'kilenc'
    ];

    private $wordSeparator = '';

    private $thousandSeparator = '-';

    private static $currencyNames = [
        'ALL' => [['lek'], ['qindarka']],
        'AUD' => [['Australian dollar'], ['cent']],
        'BAM' => [['convertible marka'], ['fenig']],
        'BGN' => [['lev'], ['stotinka']],
        'BRL' => [['real'], ['centavos']],
        'BYR' => [['Belarussian rouble'], ['kopiejka']],
        'CAD' => [['Canadian dollar'], ['cent']],
        'CHF' => [['Swiss franc'], ['rapp']],
        'CYP' => [['Cypriot pound'], ['cent']],
        'CZK' => [['Czech koruna'], ['halerz']],
        'DKK' => [['Danish krone'], ['ore']],
        'EEK' => [['kroon'], ['senti']],
        'EUR' => [['euro'], ['euro-cent']],
        'GBP' => [['pound', 'pounds'], ['pence', 'pence']],
        'HKD' => [['Hong Kong dollar'], ['cent']],
        'HRK' => [['Croatian kuna'], ['lipa']],
        'HUF' => [['forint'], ['filler']],
        'ILS' => [['new sheqel', 'new sheqels'], ['agora', 'agorot']],
        'ISK' => [['Icelandic króna'], ['aurar']],
        'JPY' => [['yen'], ['sen']],
        'LTL' => [['litas'], ['cent']],
        'LVL' => [['lat'], ['sentim']],
        'MKD' => [['Macedonian dinar'], ['deni']],
        'MTL' => [['Maltese lira'], ['centym']],
        'NOK' => [['Norwegian krone'], ['oere']],
        'PLN' => [['zloty', 'zlotys'], ['grosz']],
        'ROL' => [['Romanian leu'], ['bani']],
        'RUB' => [['Russian Federation rouble'], ['kopiejka']],
        'SEK' => [['Swedish krona'], ['oere']],
        'SIT' => [['Tolar'], ['stotinia']],
        'SKK' => [['Slovak koruna'], []],
        'TRL' => [['lira'], ['kuruþ']],
        'UAH' => [['hryvna'], ['cent']],
        'USD' => [['dollar'], ['cent']],
        'YUM' => [['dinars'], ['para']],
        'ZAR' => [['rand'], ['cent']]
    ];

    /**
     * @param int $number
     * @param int $power
     *
     * @return null|string
     */
    protected function toWords($number, $power = 0)
    {
        $return = '';

        if ($number < 0) {
            $return = $this->minus . ' ';
            $number = (int) abs($number);
        }

        if ($number == 0 || $number == '') {
            return $this->wordSeparator . self::$digits[0];
        }

        $gt2000 = $number > 2000;

        if (strlen($number) > 3) {
            $maxp = strlen($number) - 1;
            $curp = $maxp;
            for ($p = $maxp; $p > 0; --$p) { // power
                if (isset(self::$exponent[$p])) {
                    // send substr from $curp to $p
                    $snum = substr($number, $maxp - $curp, $curp - $p + 1);
                    $snum = preg_replace('/^0+/', '', $snum);

                    if ($snum !== '') {
                        $return .= $this->toWords($snum, $p);

                        if ($gt2000) {
                            $return .= $this->thousandSeparator;
                        }
                    }
                    $curp = $p - 1;
                    continue;
                }
            }

            $number = substr($number, $maxp - $curp, $curp - $p + 1);

            if ($number == 0) {
                return rtrim($return, $this->thousandSeparator);
            }
        }

        $d = $number % 10;
        $t = (int) ($number / 10) % 10;
        $h = (int) ($number / 100) % 10;

        if ($h) {
            $return .= $this->wordSeparator . self::$digits[$h] . $this->wordSeparator . 'száz';
        }

        // ten, twenty etc.
        switch ($t) {
            case 9:
            case 5:
                $return .= $this->wordSeparator . self::$digits[$t] . 'ven';
                break;
            case 8:
            case 6:
                $return .= $this->wordSeparator . self::$digits[$t] . 'van';
                break;
            case 7:
                $return .= $this->wordSeparator . 'hetven';
                break;
            case 3:
                $return .= $this->wordSeparator . 'harminc';
                break;
            case 4:
                $return .= $this->wordSeparator . 'negyven';
                break;
            case 2:
                switch ($d) {
                    case 0:
                        $return .= $this->wordSeparator . 'húsz';
                        break;
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        $return .= $this->wordSeparator . 'huszon';
                        break;
                }
                break;
            case 1:
                switch ($d) {
                    case 0:
                        $return .= $this->wordSeparator . 'tíz';
                        break;
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        $return .= $this->wordSeparator . 'tizen';
                        break;
                }
                break;
        }

        if ($d > 0) {
            $return .= $this->wordSeparator . self::$digits[$d];
        }

        if ($power > 0) {
            if (isset(self::$exponent[$power])) {
                $lev = self::$exponent[$power];
            }

            if (!isset($lev) || !is_array($lev)) {
                return null;
            }

            $return .= $this->wordSeparator . $lev[0];
        }

        return $return;
    }

    /**
     * @param string $currency
     * @param int    $decimal
     * @param int    $fraction
     *
     * @return string
     * @throws NumberToWordsException
     */
    public function toCurrencyWords($currency, $decimal, $fraction = null)
    {
        $currency = strtoupper($currency);

        if (!array_key_exists($currency, self::$currencyNames)) {
            throw new NumberToWordsException(
                sprintf('Currency "%s" is not available for "%s" language', $currency, get_class($this))
            );
        }

        $currencyNames = self::$currencyNames[$currency];

        $return = trim($this->toWords($decimal));
        $level = $decimal == 1 ? 0 : 1;

        if ($level > 0) {
            if (count($currencyNames[0]) > 1) {
                $return .= ' ' . $currencyNames[0][$level];
            } else {
                $return .= ' ' . $currencyNames[0][0] . 's';
            }
        } else {
            $return .= ' ' . $currencyNames[0][0];
        }

        if ($fraction !== null) {
            $return .= $this->wordSeparator . trim($this->toWords($fraction));

            $level = ($fraction == 1) ? 0 : 1;
            if ($level > 0) {
                if (count($currencyNames[1]) > 1) {
                    $return .= ' ' . $currencyNames[1][$level];
                } else {
                    $return .= ' ' . $currencyNames[1][0] . 's';
                }
            } else {
                $return .= ' ' . $currencyNames[1][0];
            }
        }

        return $return;
    }
}
