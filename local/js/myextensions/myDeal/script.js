BX.ready(function(){
    let wrapper = $('.workarea-content-paddings')[0];

    getDealList()
        .then(dealList => {
            let deals = dealList;
            let container = createContainer();
            let select = createSelect();
            createOptions(select, deals);
            let icon = createIcon();
            let dealInfo = createInfoDiv();

            BX.append(select, container);
            BX.append(icon, container);
            BX.append(container, wrapper);
            BX.insertAfter(dealInfo, container);

            BX.bind(select, 'change', function() {
                const selectedDeal = deals.find(deal => deal.ID == this.value);
                if (selectedDeal) {
                    $('#info').text('Сделка: ID = ' + selectedDeal.ID + ', Название = "' + selectedDeal.TITLE + '", Ответственный: ' + selectedDeal.ASSIGNED_BY_ID);

                    BX.insertAfter(dealInfo, container);
                }
            });

            let newButton = BX.create('button', {
                props: {
                    className: 'ui-btn ui-btn-primary ui-btn-md send-deal'
                },
                style: {
                    margin: '13px',
                },
                text: 'Отправить уведомление',
                events: {
                    click: function() {
                        const dealId = $('#selectDeal').val();
                        getDealData(dealId)
                            .then(dealData => {
                                showConfirm();
                            });
                    }
                }
            });

            BX.append(newButton, wrapper);
        });
});

function showConfirm()
{
    class CircleBalloon extends BX.UI.Notification.Balloon
    {
        render()
        {
            var content = this.getContent();
            return BX.create("div", {
                props: {
                    className: "circle-balloon"
                },
                children: [
                    BX.create("div", {
                        props: {
                            className: "circle-balloon-content"
                        },
                        html: BX.type.isDomNode(content) ? null : content,
                        children: BX.type.isDomNode(content) ? [content] : []
                    })
                ]
            })
        }
    }

    BX.UI.Notification.Center.notify({
        content: "<div class='dialog-block'>Уведомление успешно отправлено</div>",
        type: "CircleBalloon",
    });
}

function createIcon()
{
    return BX.create('div', {
        props: {
            className: 'ui-ctl-after ui-ctl-icon-angle'
        }
    });
}

function createOptions(select, deals)
{
    let option = BX.create('option', {
        props: {
            value: 0
        },
        text: 'Сделка не выбрана'
    });
    BX.append(option, select);

    deals.forEach(deal => {
        let option = BX.create('option', {
            props: {
                value: deal.ID
            },
            text: deal.TITLE + ' (' + deal.ID + ')'
        });
        BX.append(option, select);
    });
}

function createContainer()
{
    return BX.create('div', {
        props: {
            className: 'ui-ctl ui-ctl-after-icon ui-ctl-dropdown'
        },
        style: {
            margin: '14px',
            width: '400px'
        }
    });
}

function createSelect()
{
    return BX.create('select', {
        props: {
            className: 'ui-ctl-element',
            id: 'selectDeal'
        }
    });
}

function createInfoDiv()
{
    return BX.create('div', {
        props: {
            id: 'info',
        },
        style: {
            width: '300px',
            padding: '15px',
            margin: '30px 13px',
            backgroundColor: '#f5f5f5',
            border: '1px solid #ddd',
            borderRadius: '5px'
        },
        text: 'Выберите сделку'
    });
}

async function getDealData(dealId) {
    try {
        const result = await BX.rest.callMethod('crm.deal.myDeal', {
            dealId: dealId
        });

        if (result.error()) {
            console.error('Ошибка:', result.error());
            return null;
        }

        return result.data();
    } catch (error) {
        console.error('Ошибка вызова метода:', error);
        return null;
    }
}

async function getDealList() {
    try {
        const result = await BX.rest.callMethod('crm.deal.myDeal', {});

        if (result.error()) {
            console.error('Ошибка:', result.error());
            return null;
        }

        return result.data();
    } catch (error) {
        console.error('Ошибка вызова метода:', error);
        return null;
    }
}
