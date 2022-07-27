pimcore.registerNS("pimcore.plugin.LemonMindMessageBundle");

pimcore.plugin.LemonMindMessageBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.LemonMindMessageBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    postOpenObject: function (object, type) {

        if (object.data.general.o_className === 'Car') {
            object.toolbar.add({
                text: t('send-notification'),
                iconCls: 'pimcore_icon_comments',
                scale: 'small',
                handler: function (obj) {
                    let modal = new Ext.Window({
                        title: 'Send notification',
                        modal: true,
                        layout: 'fit',
                        width: 500,
                        height: 250,
                        items: [
                            new Ext.form.Panel({
                                layout: 'anchor',
                                url: '/admin/chatter/send-notification/' + obj.id,
                                defaults: {
                                    anchor: '100%'
                                },
                                items: [{
                                    xtype: 'combo',
                                    name: 'chatter',
                                    fieldLabel: 'Select chatter:',
                                    store: Ext.create('Ext.data.Store', {
                                        fields: ['optionName', 'value'],
                                        data: [
                                            {
                                                value: 'googlechat',
                                                optionName: 'Google Chat'
                                            },
                                            {
                                                value: 'slack',
                                                optionName: 'Slack'
                                            },
                                            {
                                                value: 'chattersAll',
                                                optionName: 'Google Chat + Slack'
                                            },
                                            {
                                                value: 'email',
                                                optionName: 'Email'
                                            },
                                            {
                                                value: 'all',
                                                optionName: 'All above'
                                            },
                                        ]
                                    }),
                                    emptyText: 'Select one...',
                                    displayField: 'optionName',
                                    valueField: 'value',
                                    allowBlank: false,
                                    margin: '5'
                                }, {
                                    xtype: 'textareafield',
                                    fieldLabel: 'Additional information (can be blank)',
                                    name: 'additionalInfo',
                                    allowBlank: true,
                                    margin: '5'
                                }],
                                buttons: [{
                                    text: 'Close',
                                    handler: function () {
                                        modal.hide();
                                    }
                                }, {
                                    text: 'Send',
                                    formBind: true, //only enabled once the form is valid
                                    disabled: true,
                                    handler: function () {
                                        let form = this.up('form').getForm();
                                        if (form.isValid()) {
                                            form.submit({
                                                success: function (form, action) {
                                                    modal.hide();
                                                    pimcore.helpers.showNotification(t("success"), t("Message sent"), "success");
                                                },
                                                failure: function (form, action) {
                                                    modal.hide();
                                                    pimcore.helpers.showNotification(t("error"), t("Error when sending message"), "error");
                                                },
                                            });
                                        }
                                    }
                                }],
                            })
                        ],
                    });

                    modal.show(this);

                }.bind(this, object)
            });
            pimcore.layout.refresh();
        }
    },
});

let LemonMindMessageBundlePlugin = new pimcore.plugin.LemonMindMessageBundle();
