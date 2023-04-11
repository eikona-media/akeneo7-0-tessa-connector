import {ReactController} from '@akeneo-pim-community/legacy-bridge/src/bridge/react';
import {DependenciesProvider} from '@akeneo-pim-community/legacy-bridge';
import {ThemeProvider} from 'styled-components';
import {pimTheme} from 'akeneo-design-system';
import React from 'react';
import {TessaSettingsApp} from 'eikona/tessa/connector/settings/settings-app';

const mediator = require('oro/mediator');

class SettingsController extends ReactController {
    reactElementToMount() {
        return (
            <DependenciesProvider>
                <ThemeProvider theme={pimTheme}>
                    <TessaSettingsApp />
                </ThemeProvider>
            </DependenciesProvider>
        );
    }

    routeGuardToUnmount() {
        return /eikona_tessa_connector_settings/;
    }

    renderRoute() {
        mediator.trigger('pim_menu:highlight:tab', {extension: 'pim-menu-tessa'});
        mediator.trigger('pim_menu:highlight:item', {extension: 'pim-menu-tessa-settings'});

        return super.renderRoute();
    }
}

export = SettingsController;
