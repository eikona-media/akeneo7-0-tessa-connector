<?php

declare(strict_types=1);

namespace Eikona\Tessa\ReferenceDataAttributeBundle\back\Application\Attribute\Subscribers;

use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\CreateAttributeHandler;
use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\CreateImageAttributeCommand;
use Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute\CreateTextAttributeCommand;
use Akeneo\ReferenceEntity\Domain\Event\ReferenceEntityCreatedEvent;
use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\AttributeAsImageReference;
use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\AttributeAsLabelReference;
use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntity;
use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntityIdentifier;
use Akeneo\ReferenceEntity\Domain\Repository\AttributeRepositoryInterface;
use Akeneo\ReferenceEntity\Domain\Repository\ReferenceEntityRepositoryInterface;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\CreateTessaAttributeCommand;
use Eikona\Tessa\ReferenceDataAttributeBundle\Attribute\Property\MaxAssets\AttributeTessaMaxAssets;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeAllowedExtensions;

/**
 * @see \Akeneo\ReferenceEntity\Application\Attribute\Subscribers\SetDefaultAttributesOnReferenceEntityCreationSubscriber
 */
class SetDefaultAttributesOnReferenceEntityCreationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ReferenceEntityRepositoryInterface $referenceEntityRepository,
        private AttributeRepositoryInterface $attributeRepository,
        private CreateAttributeHandler $createAttributeHandler,
        private Tessa $tessa,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ReferenceEntityCreatedEvent::class => 'whenReferenceEntityCreated',
        ];
    }

    public function whenReferenceEntityCreated(ReferenceEntityCreatedEvent $referenceEntityCreatedEvent): void
    {
        $referenceEntityIdentifier = $referenceEntityCreatedEvent->getReferenceEntityIdentifier();
        $this->createAttributeAsLabel($referenceEntityIdentifier);

        if ($this->tessa->isReferenceEntityTessaMainImageEnabled()) {
            $this->createTessaAttributeAsImage($referenceEntityIdentifier);
        } else {
            $this->createAttributeAsImage($referenceEntityIdentifier);
        }
        $this->updateReferenceEntityWithAttributeAsLabelAndImage($referenceEntityIdentifier);
    }

    private function createAttributeAsLabel(ReferenceEntityIdentifier $referenceEntityIdentifier): void
    {
        $createLabelAttributeCommand = new CreateTextAttributeCommand(
            $referenceEntityIdentifier->normalize(),
            ReferenceEntity::DEFAULT_ATTRIBUTE_AS_LABEL_CODE,
            [],
            false,
            false,
            true,
            null,
            false,
            false,
            'none',
            null
        );

        ($this->createAttributeHandler)($createLabelAttributeCommand);
    }

    private function createAttributeAsImage(ReferenceEntityIdentifier $referenceEntityIdentifier): void
    {
        $createImageAttributeCommand = new CreateImageAttributeCommand(
            $referenceEntityIdentifier->normalize(),
            ReferenceEntity::DEFAULT_ATTRIBUTE_AS_IMAGE_CODE,
            [],
            false,
            false,
            false,
            null,
            []
        );

        ($this->createAttributeHandler)($createImageAttributeCommand);
    }

    private function updateReferenceEntityWithAttributeAsLabelAndImage(ReferenceEntityIdentifier $referenceEntityIdentifier): void
    {
        $referenceEntity = $this->referenceEntityRepository->getByIdentifier($referenceEntityIdentifier);

        $attributes = $this->attributeRepository->findByReferenceEntity($referenceEntityIdentifier);
        foreach ($attributes as $attribute) {
            if (ReferenceEntity::DEFAULT_ATTRIBUTE_AS_LABEL_CODE === (string) $attribute->getCode()) {
                $referenceEntity->updateAttributeAsLabelReference(
                    AttributeAsLabelReference::fromAttributeIdentifier($attribute->getIdentifier())
                );
            }
            if (ReferenceEntity::DEFAULT_ATTRIBUTE_AS_IMAGE_CODE === (string) $attribute->getCode()) {
                $referenceEntity->updateAttributeAsImageReference(
                    AttributeAsImageReference::fromAttributeIdentifier($attribute->getIdentifier())
                );
            }
        }

        $this->referenceEntityRepository->update($referenceEntity);
    }

    private function createTessaAttributeAsImage(ReferenceEntityIdentifier $referenceEntityIdentifier): void
    {
        $createImageAttributeCommand = new CreateTessaAttributeCommand(
            $referenceEntityIdentifier->normalize(),
            ReferenceEntity::DEFAULT_ATTRIBUTE_AS_IMAGE_CODE,
            [],
            false,
            false,
            false,
            AttributeTessaMaxAssets::NO_LIMIT,
            AttributeAllowedExtensions::ALL_ALLOWED,
            null
        );

        ($this->createAttributeHandler)($createImageAttributeCommand);
    }
}
