document.addEventListener(pimcore.events.postOpenObject, async (e) => {
    const content = await getAjax('/admin/chatter/class');
    const responseData = Ext.decode(content)
    const allowedClasses = responseData.classes
    const allowedChatters = responseData.allowed_chatters.split(',')

    const objectClass = e.detail.object.data.general.o_className;

    if (!objectClass) {
        return
    }

    const classToSend = allowedClasses.find(element => {
        if (element.toLowerCase().includes(objectClass.toLowerCase())) {
            return true;
        }
    });

    if (!classToSend) {
        return
    }

    e.detail.object.toolbar.add({
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
                                data: allowedData.filter(d => allowedChatters.some(e => e === d.value))
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
                            handler: () => modal.hide(),
                        }, {
                            text: 'Send',
                            formBind: true,
                            disabled: true,
                            handler: function () {
                                let form = this.up('form').getForm();
                                if (!form.isValid()) {
                                    pimcore.helpers.showNotification(t("error"), t("Your form is invalid!"), "error");
                                    return
                                }

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
                        }],
                    })
                ],
            });

            modal.show(this);

        }.bind(this, e.detail.object)
    });
    pimcore.layout.refresh();
});