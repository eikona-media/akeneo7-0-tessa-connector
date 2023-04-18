<?php

namespace Eikona\Tessa\ReferenceDataAttributeBundle\Controller;

use Eikona\Tessa\ConnectorBundle\Controller\MediaFileController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FileController
{
    protected \Akeneo\Pim\Enrichment\Bundle\Controller\Ui\FileController $inner;
    protected MediaFileController $tessaMediaFileController;

    public function __construct(
        $inner,
        MediaFileController $tessaMediaFileController,
    ) {
        $this->inner = $inner;
        $this->tessaMediaFileController = $tessaMediaFileController;
    }

    /**
     * @param Request $request
     * @param string  $filename
     * @param string  $filter
     *
     * @return RedirectResponse
     */
    public function showAction(Request $request, $filename, $filter = null)
    {
        $filename = urldecode($filename);

        if (str_starts_with($filename, 'tessa-')) {
            $assetId = str_replace('tessa-', '', $filename);
            return $this->tessaMediaFileController->previewAction($assetId);
        }

        return $this->inner->showAction($request, $filename, $filter);
    }

    public function cacheAction(Request $request, $path, $filter)
    {
        return $this->inner->cacheAction($request, $path, $filter);
    }

    public function downloadAction($filename)
    {
        return $this->inner->downloadAction($filename);
    }

    public function defaultThumbnailAction($mimeType)
    {
        return $this->inner->defaultThumbnailAction($mimeType);
    }
}
