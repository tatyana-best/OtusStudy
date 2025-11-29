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

    BX.addCustomEvent("onTimeManDataRecieved", function(p, q) {
        let state = p.STATE;
        let id = p.ID;
        console.log(p);
        if ($('.tm-timer__value').length && $('.--play-l').length) {
            if (p.hasOwnProperty('STATE') && p.STATE == 'OPENED') {
                var popup = BX.PopupWindowManager.create("popup-message", BX('element'), {
                    content: '<div style="display:none;"></div>',
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
                                click: function() {
                                    BX.onCustomEvent("onTimeManDayOpen", {});

                                    $('.tm-control-panel__info:not(.tm-control-panel__info_pause) .tm-timer__value-number_hours').html('00');
                                    $('.tm-control-panel__info:not(.tm-control-panel__info_pause) .tm-timer__value-number_minutes').html('00');
                                    $('.tm-control-panel__info:not(.tm-control-panel__info_pause) .tm-timer__value-number_seconds').html('00');
                                    this.popupWindow.close();
                                    const sayHi = () => {
                                        if (i == 60) {
                                            i = 0;
                                            m ++;
                                        }
                                        if (m == 60) {
                                            m = 0;
                                            h ++;
                                        }
                                        if (h == 24) {
                                            h = 0;
                                            m = 0;
                                            i = 0;
                                        }
                                        if (i < 10) {
                                            sec = '0' + i;
                                        } else {
                                            sec = i;
                                        }
                                        if (m < 10) {
                                            min = '0' + m;
                                        } else {
                                            min = m;
                                        }
                                        if (h < 10) {
                                            let hour = '0' + h;
                                        } else {
                                            hour = h;
                                        }

                                        $('.tm-control-panel__info:not(.tm-control-panel__info_pause) .tm-timer__value-number_seconds').html(sec);
                                        $('.tm-control-panel__info:not(.tm-control-panel__info_pause) .tm-timer__value-number_minutes').html(min);
                                        $('.tm-control-panel__info:not(.tm-control-panel__info_pause) .tm-timer__value-number_hours').html(hour);

                                        i++;
                                    }
                                    let i = 0;
                                    let m = 0;
                                    let h = 0;
                                    let sec = '00';
                                    let min = '00';
                                    let hour = '00';
                                    const timerId = setInterval(sayHi, 1000);
                                }
                            }
                        }),
                        new BX.PopupWindowButton({
                            text: 'Отменить',
                            id: 'copy-btn',
                            className: 'ui-btn ui-btn-primary',
                            events: {
                                click: function() {
                                    p.STATE = state;
                                    console.log(p.STATE);
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
    });
});
