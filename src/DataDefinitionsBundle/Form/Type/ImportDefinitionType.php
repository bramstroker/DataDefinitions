<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class ImportDefinitionType extends AbstractResourceType
{
    private FormTypeRegistryInterface $formTypeRegistry;

    public function __construct(
        $dataClass,
        array $validationGroups,
        FormTypeRegistryInterface $formTypeRegistry
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->formTypeRegistry = $formTypeRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('provider', ImportProviderChoiceType::class)
            ->add('loader', LoaderChoiceType::class)
            ->add('class', ClassChoiceType::class)
            ->add('cleaner', CleanerChoiceType::class)
            ->add('filter', FilterChoiceType::class)
            ->add('runner', RunnerChoiceType::class)
            ->add('persister', PersisterChoiceType::class)
            ->add('name', TextType::class)
            ->add('objectPath', TextType::class)
            ->add('key', TextType::class)
            ->add('renameExistingObjects', CheckboxType::class)
            ->add('relocateExistingObjects', CheckboxType::class)
            ->add('createVersion', CheckboxType::class)
            ->add('stopOnException', CheckboxType::class)
            ->add('skipNewObjects', CheckboxType::class)
            ->add('skipExistingObjects', CheckboxType::class)
            ->add('omitMandatoryCheck', CheckboxType::class)
            ->add('failureNotificationDocument', IntegerType::class)
            ->add('successNotificationDocument', IntegerType::class)
            ->add('mapping', ImportMappingCollectionType::class)
            ->add('forceLoadObject', CheckboxType::class);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());

                if (null === $type) {
                    return;
                }

                if (!$formType = $this->formTypeRegistry->get($type, 'default')) {
                    $formType = NoConfigurationType::class;
                }

                $this->addConfigurationFields($event->getForm(), $formType);
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());

                if (null === $type) {
                    return;
                }

                $event->getForm()->get('provider')->setData($type);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['provider'])) {
                    return;
                }

                if (!$formType = $this->formTypeRegistry->get($data['provider'], 'default')) {
                    $formType = NoConfigurationType::class;
                }

                $this->addConfigurationFields($event->getForm(), $formType);
            });
    }

    /**
     * @param FormInterface $form
     * @param string $configurationType
     */
    protected function addConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('configuration', $configurationType);
    }

    /**
     * @param FormInterface $form
     * @param mixed $data
     * @return string|null
     */
    protected function getRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if (null !== $data && null !== $data->getProvider()) {
            return $data->getProvider();
        }

        if (null !== $form->getConfig()->hasOption('configuration_type')) {
            return $form->getConfig()->getOption('configuration_type');
        }

        return null;
    }
}
