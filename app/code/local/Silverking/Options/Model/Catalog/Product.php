<?php

class Silverking_Options_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    public function addTransparentCustomOptions()
    {
        if (!$this->hasTransparentCustomOptions()) {
            return;
        }
        foreach ($this->getTransparentCustomOptions() as $customOption) {
            $this->addOption($customOption);
        }
    }

    protected function getTransparentCustomOptions()
    {
        $result = array();
        $result[] = $this->generateCourierOption();

        if (null !== ($montage = $this->generateMontageOption())) {
            $result[] = $montage;
        }

        return $result;
    }

    protected function generateCourierOption()
    {
        $helper = Mage::helper('sko');

        $option = new Mage_Catalog_Model_Product_Option();
        $option->setOptionId('shipping');
        $option->setSortOrder(30);
        $option->setType('radio');
        $option->setProduct($this);
        $option->setIsRequire(1);
        $option->setTitle($helper->__('Wybierz dostawę'));
        $option->setDefaultTitle($option->getTitle());

        // values

        $values = array();
        $values[] = array(
            'code' => 'pickup',
            'title' => $helper->__('Odbiór osobisty'),
            'price' => 0,
        );

        if ($this->getCourier() > 0) {
            $values[] = array(
                'code' => 'courier',
                'title' => $helper->__('Wysyłka kurierem'),
                'price' => $this->getCourier(),
            );
        }

        $values[] = array(
            'code' => 'own',
            'title' => $helper->__('Nasz transport'),
            'price' => 0,
        );

        foreach ($values as $data) {
            $value = new Mage_Catalog_Model_Product_Option_Value();
            $value->setOptionTypeId($option->getOptionId() . 'v' . $data['code']);
            $value->setOptionId($option->getOptionId());
            $value->setOption($option);
            $value->setTitle($data['title']);
            $value->setPrice($data['price']);
            $value->setPriceType('fixed');
            $value->setDefaultTitle($value->getTitle());
            $value->setDefaultPrice($value->getPrice());
            $value->setDefaultPriceType($value->getPriceType());

            $option->addValue($value);
        }

        return $option;
    }

    protected function generateMontageOption()
    {
        if (!($this->getMontage() > 0)) {
            return null;
        }

        $helper = Mage::helper('sko');

        $option = new Mage_Catalog_Model_Product_Option();
        $option->setOptionId('montage');
        $option->setSortOrder(31);
        $option->setType('radio');
        $option->setProduct($this);
        $option->setIsRequire(1);
        $option->setTitle($helper->__('Wybierz montaż'));
        $option->setDefaultTitle($option->getTitle());

        $values = array(
            array(
                'code'  => 'own',
                'title' => $helper->__('We własnym zakresie'),
                'price' => 0
            ),
            array(
                'code'  => 'enabled',
                'title' => $helper->__('Namiot z montażem'),
                'price' => $this->getMontage()
            )
        );

        foreach ($values as $data) {
            $value = new Mage_Catalog_Model_Product_Option_Value();
            $value->setOptionTypeId($option->getOptionId() . 'v' . $data['code']);
            $value->setOptionId($option->getOptionId());
            $value->setOption($option);
            $value->setTitle($data['title']);
            $value->setPrice($data['price']);
            $value->setPriceType('fixed');
            $value->setDefaultTitle($value->getTitle());
            $value->setDefaultPrice($value->getPrice());
            $value->setDefaultPriceType($value->getPriceType());

            $option->addValue($value);
        }

        return $option;
    }

    protected function hasTransparentCustomOptions()
    {
        return true;
    }

    protected function _afterLoad()
    {
        $hasOptions = false;
        if ($this->getAddTransparentCustomOptions()) {
            $this->addTransparentCustomOptions();
            if (count($this->getOptions())) {
                $hasOptions = true;
            }
        }

        parent::_afterLoad();

        if (!$this->getHasOptions() && $hasOptions) {
            $this->setHasOptions(1);
        }

        $options = $this->getOptions();
        usort($options, function($option1, $option2) {

            if($option1->getSortOrder() == $option2->getSortOrder()) {
                return 0;
            }

            return $option1->getSortOrder() > $option2->getSortOrder() ? 1 : -1;
        });

        $this->_options = $options;

        return $this;
    }

}