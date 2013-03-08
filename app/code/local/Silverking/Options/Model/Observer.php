<?php

class Silverking_Options_Model_Observer
{
    public function enabledTransparentCustomOptions($event)
    {
        $product = $event->getProduct();
        $product->setAddTransparentCustomOptions(true);
    }

}