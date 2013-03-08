<?php

class Silverking_Options_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $postcodes;

    public function getKilometerPrice()
    {
        return 2;
    }

    public function getBasePostcode()
    {
        return '30-132';
    }

    public function getTransportPrice($postcode)
    {
        $dest = $postcode;
        if ($city = $this->getCityByPostcode($postcode)) {
            $dest .= ', ' . $city;
        }
        $dest .= ', Polska';
        $url  = 'from:' . $this->getBasePostcode() . ', Kraków, Polska to:' . $dest;
        $url  = 'http://maps.google.com/maps/nav?q=' . urlencode($url);
        $response = @file_get_contents($url);
        $response = json_decode(utf8_decode($response), true);

        if (!isset($response['Status']['code']) || (200 != $response['Status']['code'])) {
            return 0;
        }
        $meters = (isset($response['Directions']['Distance']['meters'])) ? $response['Directions']['Distance']['meters'] : 0;

        return $this->getKilometerPrice() * $meters / 1000;
    }

    public function getCityByPostcode($postcode)
    {
        $postcodes = $this->getPostcodes();

        return (isset($postcodes[$postcode])) ? $postcodes[$postcode] : null;
    }

    public function getPostcodes()
    {
        if (null === $this->postcodes) {
            $path = Mage::getConfig()->getModuleDir('etc', 'Silverking_Options').DS.'postcodes.csv';

            $items      = array();
            $fileHandle = fopen($path, 'r');
            while ($row = fgetcsv($fileHandle, 1000, ';')) {
                $items[$row[1]] = $row[3];
            }
            fclose($fileHandle);

            $this->postcodes = $items;
        }

        return $this->postcodes;
    }

}
