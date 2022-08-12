async function getAjax(url) {
    return new Ext.Promise(function (resolve, reject) {
        Ext.Ajax.request({
            url: url,

            success: function (response) {
                resolve(response.responseText);
            },

            failure: function (response) {
                reject(response.status);
            }
        });
    });
}