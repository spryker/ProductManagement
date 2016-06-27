<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement\Communication\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductFormAdd extends AbstractType
{

    const FIELD_DESCRIPTION = 'description';
    const FIELD_NAME = 'name';
    const FIELD_SKU = 'sku';

    const OPTION_PRODUCT_ATTRIBUTES = 'option_product_attributes';
    const LOCALIZED_ATTRIBUTES = 'localized_attributes';
    const ATTRIBUTES = 'attributes';
    const ATTRIBUTE_VALUES = 'attribute_values';

    const VALIDATION_GROUP_ATTRIBUTES = 'validation_group_attributes';
    const VALIDATION_GROUP_ATTRIBUTE_VALUES = 'validation_group_attribute_values';

    /**
     * @return string
     */
    public function getName()
    {
        return 'productAdd';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setRequired(self::ATTRIBUTES);
        $resolver->setRequired(self::ATTRIBUTE_VALUES);

        $resolver->setDefaults([
            'cascade_validation' => true,
            'required' => false,
            'validation_groups' => function (FormInterface $form) {
                return [self::VALIDATION_GROUP_ATTRIBUTES, self::VALIDATION_GROUP_ATTRIBUTE_VALUES];
            }
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addSkuField($builder)
            ->addLocalizedForm($builder)
            ->addAttributesForm($builder, $options)
            ->addAttributeValuesForm($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSkuField(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::FIELD_SKU, 'text', [
                'label' => 'SKU',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addLocalizedForm(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::LOCALIZED_ATTRIBUTES, 'collection', [
                'type' => new ProductLocalizedForm()
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAttributesForm(FormBuilderInterface $builder, $options)
    {
        $builder
            ->add(self::ATTRIBUTES, 'collection', [
                'label' => 'Attributes',
                'type' => new ProductFormAttributes(
                    $options[self::ATTRIBUTES],
                    self::VALIDATION_GROUP_ATTRIBUTES
                ),
                'constraints' => [new Callback([
                    'methods' => [
                        function ($attributes, ExecutionContextInterface $context) {
                            $selectedAttributes = [];
                            foreach ($attributes as $type => $valueSet) {
                                if (!empty($valueSet['value'])) {
                                    $selectedAttributes[] = $valueSet['value'];
                                }
                            }

                            if (empty($selectedAttributes)) {
                                $context->addViolation('Please select at least one attribute');
                            }
                        },
                    ],
                    'groups' => [self::VALIDATION_GROUP_ATTRIBUTES]
                ])]
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAttributeValuesForm(FormBuilderInterface $builder, $options)
    {
        $builder
            ->add(self::ATTRIBUTE_VALUES, 'collection', [
                'label' => 'Attribute Values',
                'type' => new ProductFormAttributeValues(
                    $options[self::ATTRIBUTE_VALUES],
                    self::VALIDATION_GROUP_ATTRIBUTE_VALUES
                ),
                'constraints' => [new Callback([
                    'methods' => [
                        function ($attributes, ExecutionContextInterface $context) {
                            $selectedAttributes = [];
                            foreach ($attributes as $type => $valueSet) {
                                if (!empty($valueSet['value'])) {
                                    $selectedAttributes[] = $valueSet['value'];
                                }
                            }

                            if (empty($selectedAttributes)) {
                                $context->addViolation('Please select at least one attribute value');
                            }
                        },
                    ],
                    'groups' => [self::VALIDATION_GROUP_ATTRIBUTE_VALUES]
                ])]
            ]);

        return $this;
    }

}
