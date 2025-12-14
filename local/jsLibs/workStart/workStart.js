BX.ready(function() {
    let originalBxOnCustomEvent = BX.onCustomEvent;

    BX.onCustomEvent = function (eventObject, eventName, eventParams, secureParams)
    {
        let realEventName = BX.type.isString(eventName) ?
            eventName : BX.type.isString(eventObject) ? eventObject : null;

        if (realEventName) {
            console.log(
                '%c' + realEventName,
                'background: #222; color: #bada55; font-weight: bold; padding: 3px 4px;'
            );
        }

        console.dir({
            eventObject: eventObject,
            eventParams: eventParams,
            secureParams: secureParams
        });

        originalBxOnCustomEvent.apply(
            null, arguments
        );
    };

    BX.addCustomEvent("onTimeManDataRecieved", function (data) {
        $('.my-start').remove();
        if ((data.hasOwnProperty('STATE') && data.STATE != 'OPENED')) {
            console.log(data.STATE);
            setTimeout(() => {
                window.opa = function()
                {
                    var popupStart = BX.PopupWindowManager.create("popup-message", BX('element'), {
                        content: '',
                        width: 250,
                        height: 150,
                        zIndex: 100,
                        closeIcon: {
                            opacity: 1
                        },
                        titleBar: 'Начать рабочий день?',
                        closeByEsc: true,
                        autoHide: true,
                        resizable: true,
                        min_height: 100,
                        min_width: 100,
                        overlay: {
                            backgroundColor: 'black',
                            opacity: 500
                        },
                        buttons: [
                            new BX.PopupWindowButton({
                                text: 'Да',
                                id: 'save-btn',
                                className: 'ui-btn ui-btn-success',
                                events: {
                                    click: function () {
                                        $('.ui-btn.--air.tm-control-panel__action.ui-btn-lg.--wide.--style-filled.ui-btn-no-caps.--with-icon').click();
                                        this.popupWindow.close();
                                    }
                                }
                            }),
                            new BX.PopupWindowButton({
                                text: 'Отменить',
                                id: 'copy-btn',
                                className: 'ui-btn ui-btn-primary',
                                events: {
                                    click: function () {
                                        this.popupWindow.close();
                                    }
                                }
                            })
                        ],
                    });
                    popupStart.show();
                    $('#popup-window-content-popup-message').hide();
                }
                $('.tm-control-panel__actions-list > .tm-control-panel__actions-item').first().hide();
                let html = '<li class="tm-control-panel__actions-item my-start">';
                html += '<button onclick="opa()" class="ui-btn --air tm-control-panel__action ui-btn-lg --wide --style-filled ui-btn-no-caps --with-icon">';
                html += '<div class="ui-icon-set --play-l"></div>';
                html += '<span class="ui-btn-text"><span class="ui-btn-text-inner">Начать рабочий день</span></span>';
                html += '</button></li>'
                $('.tm-control-panel__actions-list').prepend(html);
            }, 500);
        }
    });
});
