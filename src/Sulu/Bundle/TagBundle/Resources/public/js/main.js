/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require.config({
    paths: {
        sulutag: '../../sulutag/js',
        "type/tagList": '../../sulutag/js/validation/types/tagList'
    }
});

define({

    name: "SuluTagBundle",

    initialize: function(app) {

        'use strict';

        var sandbox = app.sandbox;

        app.components.addSource('sulutag', '/bundles/sulutag/js/components');

        // Example: list all contacts
        // sandbox.mvc.routes.push({
        //     route: 'contacts/contacts',
        //    callback: function(){
        //         this.html('<div data-aura-component="contacts@sulucontact" data-aura-display="list"/>');
        //     }
        // });
    }
});
