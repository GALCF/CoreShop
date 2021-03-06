<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Product\Cloner;

use CoreShop\Component\Core\Model\ProductInterface;

class ProductUnitDefinitionsCloner implements ProductClonerInterface
{
    /**
     * {@inheritDoc}
     */
    public function clone(ProductInterface $product, ProductInterface $referenceProduct, bool $resetExistingData = false)
    {
        if ($product->hasUnitDefinitions() === true && $resetExistingData === false) {
            return;
        }

        $unitDefinitions = clone $referenceProduct->getUnitDefinitions();

        $product->setUnitDefinitions($unitDefinitions);
    }
}
