<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement\Communication\Form\DataProvider;

use Spryker\Zed\ProductManagement\Communication\Form\ProductFormAdd;

class ProductFormEditDataProvider extends AbstractProductFormDataProvider
{

    /**
     * @param int $idProductAbstract
     *
     * @return array
     */
    public function getData($idProductAbstract)
    {
        $formData = [];
        $defaults = $this->getDefaultFormFields();

        $productAbstractTransfer = $this->productFacade->getProductAbstractById($idProductAbstract);
        if ($productAbstractTransfer) {
            $formData = $productAbstractTransfer->toArray(true);
            $formData[ProductFormAdd::LOCALIZED_ATTRIBUTES] = $this->getLocalizedAbstractAttributes($productAbstractTransfer);
        }

        return array_merge($defaults, $formData);
    }

    /**
     * @return array
     */
    protected function getDefaultFormFields()
    {
        return [
            ProductFormAdd::FIELD_SKU => null,
            ProductFormAdd::LOCALIZED_ATTRIBUTES => $this->getAttributesDefaultFields()
        ];
    }

}
