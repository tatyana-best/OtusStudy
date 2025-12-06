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

    // События, которые подходят по логике:
    //onTimeManNeedRebuild, onTimeManDataRecieved, onTaskTimerChange, onTimeManDayOpen
    // А вот это вообще не ловится: onTimeManWindowOpen

    BX.addCustomEvent("onTimeManDataRecieved", function(p) {
        //alert(12345678);
        if ($('.tm-timer__value').length && $('.--play-l').length) {
           if (p.hasOwnProperty('STATE') && p.STATE == 'OPENED') {
               $('ul.tm-control-panel__actions-list li').hide();
                var popup = BX.PopupWindowManager.create("popup-message", BX('element'), {
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
                                    //p.STATE = state;
                                    //console.log(p.STATE);
                                    this.popupWindow.close();
                                }
                            }
                        })
                    ],
                });
                popup.show();
                $('#popup-window-content-popup-message').hide();
            }
        }
    })
});
