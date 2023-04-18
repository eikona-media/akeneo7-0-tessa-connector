import React, { useState } from 'react';
import { PageContent, PageHeader, PimView, useTranslate } from '@akeneo-pim-community/shared';
import {
  Breadcrumb,
  Button,
} from 'akeneo-design-system';
import styled from 'styled-components';

const Routing = require('routing');
import { useEffect } from 'react';

const HeaderIcon = styled.div`
  background-image: url(/bundles/eikonatessaconnector/images/logo_tessa.svg);
  background-size: 100px;
  min-width: 150px;
`;

const TessaInfoApp = () => {
  const translate = useTranslate();
  const [infos, setInfos] = useState<{
    version: string;
    tessaLink: string | null;
  } | null>(null);

  useEffect(() => {
    fetch(Routing.generate('eikona_tessa_internal_api_connector_infos'))
      .then((r) => r.json())
      .then((infos) => setInfos({
        version: infos.version,
        tessaLink: infos.tessaLink,
      }))
  }, []);

  return (
    <>
      <PageHeader>
        <PageHeader.Breadcrumb>
          <Breadcrumb>
            <Breadcrumb.Step>{translate('eikona.tessa.menu.tessa')}</Breadcrumb.Step>
            <Breadcrumb.Step>{translate('eikona.tessa.menu.info')}</Breadcrumb.Step>
          </Breadcrumb>
        </PageHeader.Breadcrumb>
        <PageHeader.UserActions>
          <PimView
            viewName="pim-menu-user-navigation"
            className="AknTitleContainer-userMenuContainer AknTitleContainer-userMenu"
          />
        </PageHeader.UserActions>
        <PageHeader.Title>{translate('eikona.tessa.menu.info')}</PageHeader.Title>
      </PageHeader>
      <PageContent>
        {infos && (
          <>
            <div className="AknDescriptionHeader">
              <HeaderIcon className={"AknDescriptionHeader-icon"}/>
              <div className="AknDescriptionHeader-title">
                {translate('eikona.tessa.info.name')}
                <div className="AknDescriptionHeader-description">
                  <p>
                    {translate('eikona.tessa.info.version')}&nbsp;
                    {infos.version}
                  </p>
                </div>
              </div>
            </div>
            {infos.tessaLink && (
              <Button level={"secondary"} href={infos.tessaLink} target="_blank">
                {translate("eikona.tessa.info.gotoTessaButtonLabel")}
              </Button>
            )}
          </>
        )}
      </PageContent>
    </>
  );
};


export { TessaInfoApp };
