BX.namespace('OtusMain.CarGrid');

BX.OtusMain.CarGrid = {
    signedParams: null,
    init: function(data) {
        this.signedParams = data.signedParams;
    },

    showMessage: function (message) {
        alert(message);
    },

    showForm: function (id) {
        var data_save = {
            id: id,
        };
        BX.ajax({
            url: '/bitrix/components/OtusMain/car.grid/templates/.default/ajax.php',
            method: 'POST',
            data: data_save,
            dataType: 'json',
            onsuccess: function ($data) {
                let getData = JSON.parse(JSON.stringify($data));
                console.log(getData);
                let html = '<h3>' + getData.MODEL + ' ' + getData.MARKA + ' ' + getData.NUMBER + '</h3>';
                    html += '<table class="deal-table">';
                if (getData['ITEMS'] === undefined) {
                    html += '<tr>';
                    html += 'Заявок на ремонт нет';
                    html += '</tr>';
                    html += '</table>';
                } else {
                    html += '<tr>';
                    html += '<td>Название</td>';
                    html += '<td>Дата создания</td>';
                    html += '<td>Стадия</td>';
                    html += '<td>Ответственный</td>';
                    html += '<td>Сумма</td>';
                    html += '<td>Список запчастей</td>';
                    html += '</tr>';
                    let deals = Object.values(getData.ITEMS);
                    deals.forEach(deal => {
                        html += '<tr>';
                        html += '<td>' + deal.TITLE + '</td>';
                        html += '<td>' + deal.DATE_CREATE + '</td>';
                        html += '<td>' + deal.STAGE_ID + '</td>';
                        html += '<td>' + deal.ASSIGNED_BY_ID + '</td>';
                        html += '<td>' + deal.OPPORTUNITY + '</td>';
                        html += '<td>';
                        html += '<ul>';
                        if (deal.PRODUCTS) {
                            let products = Object.values(deal.PRODUCTS);
                            products.forEach(product => {
                                html += '<li>' + product.NAME + '  ' + product.PRICE + '</li>';
                            });
                        }
                        html += '</ul>';
                        html += '</td>';
                        html += '</tr>';
                    });

                    html += '</table>';
                }
                let popup = [];
                popup[getData.ID] = BX.PopupWindowManager.create('book-add-form-' + getData.ID, null, {
                    content: html,
                    darkMode: true,
                    buttons: [
                        new BX.PopupWindowButton({
                            text: "Закрыть" ,
                            className: "book-form-button-link-cancel" ,
                            events: {
                                click: function(){
                                    this.popupWindow.close();
                                }
                            }
                        })
                    ]
                });
                popup[getData.ID].show();
            },
            onfailure: function ($data) {
                console.error();
            }
        });
    },

    addBook: function (id) {
        BX.OtusMain.CarGrid.showForm(id);
    },
}
