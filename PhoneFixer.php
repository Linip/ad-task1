<?php


class PhoneFixer
{
    public function __construct($rules = [])
    {
        $this->rules = array_merge($this->rules, $rules);
    }

    protected $rules = [
        'LT' => [
            'countryCode' => 'LT',
            'minNumbers' => 8,
            'maxNumbers' => 8,
            'phoneCode' => '+370'
        ],

        'RU' => [
            'countryCode' => 'RU',
            'minNumbers' => 10,
            'maxNumbers' => 10,
            'phoneCode' => '+7'
        ],
    ];

    /**
     * @param string $countryCode
     * @param string|int $phoneNumber
     * @return array
     */
    public function fix($countryCode, $phoneNumber)
    {
        if (!isset($this->rules[$countryCode]))
            return $this->fail('Кода страны нет в справочнике');

        $rule = $this->rules[$countryCode];

        $clearPhone = preg_replace('/\D/', '', $phoneNumber);
        $clearCode = preg_replace('/\D/', '', $rule['phoneCode']);

        $numberHasCountryCode = strpos($clearPhone, $clearCode) === 0;
        if ($numberHasCountryCode) {
            $withoutCode = substr($clearPhone, strlen($clearCode),  strlen($clearPhone) - strlen($clearCode));
        } else
            $withoutCode = $clearPhone;

        $numberIsTooLong = strlen($withoutCode) > $rule['maxNumbers'];
        if ($numberIsTooLong)
            return $this->fail('Номер слишком длинный');

        $numberIsTooShort = strlen($withoutCode) < $rule['minNumbers'];
        if ($numberIsTooShort)
            $this->fail('Номер слишком короткий');

        return $this->success($clearCode.$withoutCode);
    }

    protected function success($phoneNumber) {
        return [
            'isValid' => true,
            'phoneNumber' => (int) $phoneNumber
        ];
    }
    protected function fail($message) {
        return [
            'isValid' => false,
            'message' => $message
        ];
    }
}