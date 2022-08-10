pimcore.registerNS("pimcore.plugin.LemonmindMessageBundle");

pimcore.plugin.LemonmindMessageBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.LemonmindMessageBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },


    postOpenObject: function (object, type) {

        Ext.Ajax.request({
            url: '/admin/chatter/class',
            success: function (response) {
                let data = Ext.decode(response.responseText);
                let objectClasses = object.data.general.php.classes;

                function contains(value) {
                    for (let i = 0; i < data.classes.length; i++) {
                        if (value.includes(data.classes[i])) {
                            return value
                        }
                    }
                    return null
                }

                let classToSend = objectClasses.filter(contains);
                if (classToSend.length > 0) {
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
                                                        value: 'discord',
                                                        optionName: 'Discord'
                                                    },
                                                    {
                                                        value: 'googlechat',
                                                        optionName: 'Google Chat'
                                                    },
                                                    {
                                                        value: 'slack',
                                                        optionName: 'Slack'
                                                    },
                                                    {
                                                        value: 'telegram',
                                                        optionName: 'Telegram'
                                                    },
                                                    {
                                                        value: 'email',
                                                        optionName: 'Email'
                                                    },
                                                    {
                                                        value: 'sms',
                                                        optionName: 'Sms'
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
                                        },
                                            {
                                                xtype: 'hiddenfield',
                                                name: 'classToSend',
                                                value: classToSend,
                                            }
                                        ],

                                        buttons: [{
                                            text: 'Close',
                                            handler: function () {
                                                modal.hide();
                                            }
                                        }, {
                                            text: 'Send',
                                            formBind: true,
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
            }
        });
    },
});

let LemonmindMessageBundlePlugin = new pimcore.plugin.LemonmindMessageBundle();
