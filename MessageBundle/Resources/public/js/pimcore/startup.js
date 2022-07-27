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
                    Ext.Ajax.request({
                        url: '/admin/slack/send-notification/' + obj.id,
                        success: function (response) {
                            let data = Ext.decode(response.responseText);
                            if (data.success) {
                                pimcore.helpers.showNotification(t("success"), t("Message sent"), "success");
                            } else {
                                pimcore.helpers.showNotification(t("error"), t("Error when sending message"), "error");
                            }
                        }
                    });

                }.bind(this, object)
            });
            pimcore.layout.refresh();
        }
    },
});

let LemonMindMessageBundlePlugin = new pimcore.plugin.LemonMindMessageBundle();
