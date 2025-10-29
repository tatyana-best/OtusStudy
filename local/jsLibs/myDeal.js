BX.ready(function() {
    alert('7556765');


    let originalBxOnCustomEvent = BX.onCustomEvent;

    BX.onCustomEvent = function (eventObject, eventName, eventParams, secureParams)
    {
        // onMenuItemHover например выбрасывает в другом порядке
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

    // Подписываемся на событие инициализации табов
    BX.addCustomEvent('BX.Crm.EntityEditorControllerFactory:onInitialize', function(tabs, entity) {
        console.log('Табы инициализированы:', tabs);
        console.log('Сущность:', entity);

        // Добавляем кастомный таб
        addCustomTab(tabs, entity);
    });
});

function addCustomTab(tabs, entity) {
    // Создаем новый таб
    const customTab = {
        id: 'custom_tab',
        name: 'Мой кастомный таб',
        enabled: true,
        active: false,
        fields: []
    };

    // Добавляем таб в коллекцию
    tabs.add(customTab);

    // Подписываемся на активацию таба
    BX.addCustomEvent(tabs, 'onTabActivate', function(tab) {
        if (tab.id === 'custom_tab') {
            loadCustomTabContent(tab, entity);
        }
    });
}
