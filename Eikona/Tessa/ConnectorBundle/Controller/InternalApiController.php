<?php

namespace Eikona\Tessa\ConnectorBundle\Controller;

use Eikona\Tessa\ConnectorBundle\Tessa;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\SecurityBundle\SecurityFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class InternalApiController
{
    public function __construct(
        protected ConfigManager $oroGlobal,
        protected Tessa $tessa,
        protected SecurityFacade $security,
    )
    {
    }

    public function infoAction(): JsonResponse
    {
        $this->denyAccessUnlessAclIsGranted('eikona_tessa_connector_info');

        return new JsonResponse([
            'version' => '1.0.0',
            'tessaLink' => $this->tessa->getUiUrl() ?: null,
        ]);
    }

    public function getSettingsAction(): JsonResponse
    {
        $this->denyAccessUnlessAclIsGranted('eikona_tessa_connector_settings');

        return new JsonResponse([
            $this->viewName('base_url') => $this->oroGlobal->get($this->modelName('base_url')),
            $this->viewName('ui_url') => $this->oroGlobal->get($this->modelName('ui_url')),
            $this->viewName('use_http_internally') => (bool)$this->oroGlobal->get($this->modelName('use_http_internally')),
            $this->viewName('username') => $this->oroGlobal->get($this->modelName('username')),
            $this->viewName('api_key') => $this->oroGlobal->get($this->modelName('api_key')),
            $this->viewName('system_identifier') => $this->oroGlobal->get($this->modelName('system_identifier')),
            $this->viewName('disable_asset_editing_in_akeneo_ui') => (bool)$this->oroGlobal->get($this->modelName('disable_asset_editing_in_akeneo_ui')),
            $this->viewName('enable_reference_entity_tessa_main_image') => (bool)$this->oroGlobal->get($this->modelName('enable_reference_entity_tessa_main_image')),
        ]);
    }

    public function saveSettingsAction(
        Request $request
    ): JsonResponse
    {
        $this->denyAccessUnlessAclIsGranted('eikona_tessa_connector_settings');

        $settings = json_decode($request->getContent(), true);
        $settings = array_map(fn($setting) => [
            'value' => $setting,
            'scope' => $this->oroGlobal->getScopedEntityName(),
            'use_parent_scope_value' => false,
        ], $settings);


        $this->oroGlobal->save($settings);
        return $this->getSettingsAction();
    }

    private function viewName(string $key): string
    {
        return 'pim_eikona_tessa_connector' . $this->oroGlobal::SECTION_VIEW_SEPARATOR . $key;
    }

    private function modelName(string $key): string
    {
        return 'pim_eikona_tessa_connector' . $this->oroGlobal::SECTION_MODEL_SEPARATOR . $key;
    }

    private function denyAccessUnlessAclIsGranted(string $acl): void
    {
        if (!$this->security->isGranted($acl)) {
            throw new AccessDeniedHttpException('Access forbidden.');
        }
    }
}
