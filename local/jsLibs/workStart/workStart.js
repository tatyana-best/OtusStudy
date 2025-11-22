BX.ready(function() {
    let originalBxOnCustomEvent = BX.onCustomEvent;

    BX.onCustomEvent = function (eventObject, eventName, eventParams, secureParams)
    {
        // onMenuItemHover например выбрасывает в другом порядке
        //if ( eventName == 'onTimeManDataRecieved') {
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
        //}
    };

    // События, которые подходят по логике:
    //onTimeManNeedRebuild, onTimeManDataRecieved, onTaskTimerChange
    // А вот это вообще не ловится: onTimeManWindowOpen

    BX.addCustomEvent("onTimeManDataRecieved", function(p) {
        console.log(p);
        //p.INFO.TIME_START = '';
        let state = p.STATE;
        //console.log(p.STATE);
        if (p.hasOwnProperty('STATE') && p.STATE == 'OPENED' && p.hasOwnProperty('INFO') && p.INFO.hasOwnProperty('DATE_START')) {
            p.STATE = 'PAUSED';
            p.INFO.DATE_START = '';//Date.now();
            p.INFO.DATE_FINISH = '';
            p.INFO.TIME_START = '';//Date.now();
            p.INFO.TIME_FINISH = '';
            //p.LAST_PAUSE.DATE_START = '';//Date.now();
            //p.LAST_PAUSE.DATE_FINISH = '';
            //p.INFO.DATE_START = '';
            //p.CAN_EDIT = 'N';

            console.log(p);
            //console.log('Событие остановлено');

            console.log(p.DATA);
            var popup = BX.PopupWindowManager.create("popup-message", BX('element'), {
                width: 400,
                height: 200,
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
                                p.INFO.DATE_START = Date.now();
                                p.INFO.DATE_FINISH = '';
                                //p.CAN_EDIT = 'Y';
                                p.STATE = 'OPENED';
                                BX.onCustomEvent('onTimeManNeedRebuild', function() {
                                    // p.STATE = 'OPENED';
                                    // p.INFO.DATE_START = '';//Date.now();
                                    // p.INFO.DATE_FINISH = '';
                                    // p.CAN_EDIT = 'Y';
                                });
                                BX.onCustomEvent('onTaskTimerChange', function() {
                                    console.log('Custom event handler called');
                                });
                                console.log('---------------');
                                console.log(p);
                                this.popupWindow.close();
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
            return false;
        }
        //}*/
    });
});
