<?php
/**
 * ProductPdfRenderer.php
 *
 * @author      Felix Hack <f.hack@eikona-media.de>
 * @copyright   2019 EIKONA Media (https://eikona-media.de)
 */

namespace Eikona\Tessa\ConnectorBundle\PdfGeneration\Renderer;

use Akeneo\Pim\Enrichment\Bundle\PdfGeneration\Builder\PdfBuilderInterface;
use Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Eikona\Tessa\ConnectorBundle\Security\AuthGuard;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Twig\Environment;

class ProductPdfRenderer extends \Akeneo\Pim\Enrichment\Bundle\PdfGeneration\Renderer\ProductPdfRenderer
{
    use ProductPdfRendererTrait;

    public function __construct(
        Tessa $tessa,
        AuthGuard $authGuard,
        Environment $templating,
        PdfBuilderInterface $pdfBuilder,
        DataManager $dataManager,
        CacheManager $cacheManager,
        FilterManager $filterManager,
        IdentifiableObjectRepositoryInterface $attributeRepository,
        string $template,
        ?string $customFont = null
    )
    {
        $this->tessa = $tessa;
        $this->authGuard = $authGuard;

        parent::__construct(
            $templating,
            $pdfBuilder,
            $dataManager,
            $cacheManager,
            $filterManager,
            $attributeRepository,
            $template,
            $customFont
        );
    }
}
