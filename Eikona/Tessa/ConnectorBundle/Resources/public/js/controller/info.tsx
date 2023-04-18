import {ReactController} from '@akeneo-pim-community/legacy-bridge/src/bridge/react';
import {DependenciesProvider} from '@akeneo-pim-community/legacy-bridge';
import {ThemeProvider} from 'styled-components';
import {pimTheme} from 'akeneo-design-system';
import React from 'react';
import {TessaInfoApp} from 'eikona/tessa/connector/settings/info-app';

const mediator = require('oro/mediator');

class InfoController extends ReactController {
    reactElementToMount() {
        return (
            <DependenciesProvider>
                <ThemeProvider theme={pimTheme}>
                    <TessaInfoApp />
                </ThemeProvider>
            </DependenciesProvider>
        );
    }

    routeGuardToUnmount() {
        return /eikona_tessa_connector_info/;
    }

    renderRoute() {
        mediator.trigger('pim_menu:highlight:tab', {extension: 'pim-menu-tessa'});
        mediator.trigger('pim_menu:highlight:item', {extension: 'pim-menu-tessa-info'});

        return super.renderRoute();
    }
}

export = InfoController;
