<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Shipping\Rule\Action;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;

interface CarrierPriceActionProcessorInterface
{
    /**
     * @param CarrierInterface $carrier
     * @param AddressInterface $address
     * @param array            $configuration
     * @param bool             $withTax
     *
     * @return mixed
     */
    public function getPrice(CarrierInterface $carrier, AddressInterface $address, array $configuration, $withTax = true);

    /**
     * @param CarrierInterface $carrier
     * @param AddressInterface $address
     * @param $price
     * @param array $configuration
     *
     * @return mixed
     */
    public function getModification(CarrierInterface $carrier, AddressInterface $address, $price, array $configuration);
}
