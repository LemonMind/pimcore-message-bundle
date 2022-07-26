pimcore.registerNS("pimcore.plugin.LemonMindMessageBundle");

pimcore.plugin.LemonMindMessageBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.LemonMindMessageBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        //alert("LemonMindMessageBundle ready!");
    },

    postOpenObject: function (object, type) {

        if (object.data.general.o_className === 'Car') {
            object.toolbar.add({
                text: t('send-notification'),
                iconCls: 'pimcore_icon_comments',
                scale: 'small',
                handler: function (obj) {
                    window.open("http://localhost/lemonmind_message/" + object.id, '_blank');
                    //window.location.href = "http://localhost/lemon_mind_message/" + object.id;
                }.bind(this, object)
            });
            pimcore.layout.refresh();
        }
    },
});

var LemonMindMessageBundlePlugin = new pimcore.plugin.LemonMindMessageBundle();
