<?php
/**
 * ProductPdfRendererEE.php
 *
 * @author      Felix Hack <f.hack@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\PdfGeneration\Renderer;

use Akeneo\Channel\Component\Repository\ChannelRepositoryInterface;
use Akeneo\Channel\Component\Repository\LocaleRepositoryInterface;
use Akeneo\Pim\Enrichment\Bundle\PdfGeneration\Builder\PdfBuilderInterface;
use Akeneo\Pim\WorkOrganization\Workflow\Bundle\Helper\FilterProductValuesHelper;
use Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Eikona\Tessa\ConnectorBundle\Security\AuthGuard;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

class ProductPdfRendererEE extends \Akeneo\Pim\Permission\Bundle\Pdf\ProductPdfRenderer
{
    use ProductPdfRendererTrait;

    public function __construct(
        Tessa $tessa,
        AuthGuard $authGuard,
        Environment $templating,
        PdfBuilderInterface $pdfBuilder,
        FilterProductValuesHelper $filterHelper,
        DataManager $dataManager,
        CacheManager $cacheManager,
        FilterManager $filterManager,
        IdentifiableObjectRepositoryInterface $attributeRepository,
        ChannelRepositoryInterface $channelRepository,
        LocaleRepositoryInterface $localeRepository,
        string $template,
        AuthorizationCheckerInterface $authorizationChecker,
        ?string $customFont = null
    )
    {
        $this->tessa = $tessa;
        $this->authGuard = $authGuard;

        parent::__construct(
            $templating,
            $pdfBuilder,
            $filterHelper,
            $dataManager,
            $cacheManager,
            $filterManager,
            $attributeRepository,
            $channelRepository,
            $localeRepository,
            $template,
            $authorizationChecker,
            $customFont
        );
    }
}
