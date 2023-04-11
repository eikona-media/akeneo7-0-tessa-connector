import React from 'react';
import {
  NotificationLevel,
  PageContent,
  PageHeader,
  PimView,
  UnsavedChanges,
  useNotify,
  useTranslate
} from '@akeneo-pim-community/shared';
import {
  Button,
  Breadcrumb,
  Field as FieldWithoutMargin,
  Helper,
  TextInput,
  BooleanInput,
  SectionTitle,
} from 'akeneo-design-system';
import styled from 'styled-components';

const Routing = require('routing');
import { useCallback, useState, useEffect } from 'react';

const fieldConfig = [
  {
    label: 'Connection',
    fields: [
      {
        field: 'pim_eikona_tessa_connector___ui_url',
        type: 'text',
        translationKey: 'uiUrl'
      },
      {
        field: 'pim_eikona_tessa_connector___base_url',
        type: 'text',
        translationKey: 'baseUrl'
      },
      {
        field: 'pim_eikona_tessa_connector___username',
        type: 'text',
        translationKey: 'userName'
      },
      {
        field: 'pim_eikona_tessa_connector___api_key',
        type: 'text',
        translationKey: 'apiKey'
      },
      {
        field: 'pim_eikona_tessa_connector___system_identifier',
        type: 'text',
        translationKey: 'systemIdentifier'
      },
      {
        field: 'pim_eikona_tessa_connector___use_http_internally',
        type: 'checkbox',
        translationKey: 'useHttpInternally'
      },
    ]
  },
  {
    label: 'Settings',
    fields: [
      {
        field: 'pim_eikona_tessa_connector___disable_asset_editing_in_akeneo_ui',
        type: 'checkbox',
        translationKey: 'disableAssetEditingInAkeneoUi'
      },
      {
        field: 'pim_eikona_tessa_connector___enable_reference_entity_tessa_main_image',
        type: 'checkbox',
        translationKey: 'enableReferenceEntityTessaMainImage'
      },
    ]
  }
];

const Section = styled.div`
  display: flex;
  flex-direction: column;
  margin-top: 20px;
`;

const Field = styled(FieldWithoutMargin)`
  margin-top: 20px;
`;

const TessaSettingsApp = () => {
  const [settings, setSettings] = useState<{
    'pim_eikona_tessa_connector.base_url': any,
    'pim_eikona_tessa_connector.ui_url': any,
    'pim_eikona_tessa_connector.username': any,
    'pim_eikona_tessa_connector.api_key': any,
    'pim_eikona_tessa_connector.system_identifier': any,
    'pim_eikona_tessa_connector.use_http_internally': any,
    'pim_eikona_tessa_connector.disable_asset_editing_in_akeneo_ui': any,
    'pim_eikona_tessa_connector.enable_reference_entity_tessa_main_image': any,
  } | null>(null);
  const [isModified, setIsModified] = useState(false);

  const translate = useTranslate();
  const notify = useNotify();

  const handleChange = useCallback(
    (fieldName: string) => {
      return (value: boolean | string) => {
        if (!settings) return;
        modifyConfig({
          ...settings,
          [fieldName]: value,
        });
      };
    },
    [settings]
  );

  const modifyConfig = (newSettings: any) => {
    setSettings(newSettings);
    setIsModified(true);
  };

  const handleSave = () => {
    fetch(Routing.generate('eikona_tessa_internal_api_save_settings'), {
      method: "POST",
      headers: [
        ['Content-type', 'application/json'],
        ['X-Requested-With', 'XMLHttpRequest'],
      ],
      body: JSON.stringify(settings),
    })
      .then((r) => r.json())
      .then((data) => {
        setSettings(data);
        setIsModified(false);
        notify(NotificationLevel.SUCCESS, translate('eikona.tessa.settings.messages.saveSuccessful'));
      })
      .catch(() => {
        notify(NotificationLevel.ERROR, translate('eikona.tessa.settings.messages.saveFailed'));
      });
  };

  useEffect(() => {
    fetch(Routing.generate('eikona_tessa_internal_api_get_settings'))
      .then((r) => r.json())
      .then((data) => setSettings(data));
  }, []);

  return (
    <>
      <PageHeader>
        <PageHeader.Breadcrumb>
          <Breadcrumb>
            <Breadcrumb.Step>{translate('eikona.tessa.menu.tessa')}</Breadcrumb.Step>
            <Breadcrumb.Step>{translate('eikona.tessa.menu.settings')}</Breadcrumb.Step>
          </Breadcrumb>
        </PageHeader.Breadcrumb>
        <PageHeader.UserActions>
          <PimView
            viewName="pim-menu-user-navigation"
            className="AknTitleContainer-userMenuContainer AknTitleContainer-userMenu"
          />
        </PageHeader.UserActions>
        <PageHeader.Actions>
          <Button className="AknButton--apply" onClick={handleSave}>
            Save
          </Button>
        </PageHeader.Actions>
        {isModified && (
          <PageHeader.State>
            <UnsavedChanges/>
          </PageHeader.State>
        )}
        <PageHeader.Title>{translate('eikona.tessa.menu.settings')}</PageHeader.Title>
      </PageHeader>
      <PageContent>
        {settings && fieldConfig.map((c, i) => (
          <Section key={`section-${i}`}>
            <SectionTitle>
              <SectionTitle.Title>{translate(c.label)}</SectionTitle.Title>
            </SectionTitle>
            {c.fields.map((f) => (
              <Field
                key={f.field}
                label={translate('eikona.tessa.settings.fields.' + f.translationKey + '.label')}
                className={''}
              >
                {f.type === 'text' && (
                  <TextInput
                    value={settings[f.field] ?? ""}
                    onChange={handleChange(f.field)}/>
                )}
                {f.type === 'checkbox' && (
                  <BooleanInput
                    value={settings[f.field] ?? false}
                    yesLabel={translate('pim_common.yes')}
                    noLabel={translate('pim_common.no')}
                    onChange={handleChange(f.field)}/>
                )}
                <Helper>{translate('eikona.tessa.settings.fields.' + f.translationKey + '.description')}</Helper>
              </Field>
            ))}
          </Section>
        ))}
      </PageContent>
    </>
  );
};


export { TessaSettingsApp };
