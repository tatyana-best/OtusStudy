BX.ready(function() {
    alert('234123');
    BX.addCustomEvent("SidePanel.Slider:onLoad", function(p) {
        let toolbarRight = $('.ui-toolbar-right-buttons');
        if($('.ui-toolbar-right-buttons .loadDoc').length <= 0) {
            let templateId = 2; // number of document template
            let url = window.location.href;
            let ownerID = url.split('/')[6];
            let link = '\'\/bitrix\/components\/bitrix\/crm\.document\.view\/slider\.php';
                link += '\?providerClassName=Bitrix\\\\Crm\\\\Integration\\\\DocumentGenerator\\\\DataProvider\\\\Deal';
                link += '\&templateId='+templateId+'\&value='+ownerID+'\&analyticsLabel=generateDocument\&templateCode=null\'';
            let qq = '&quot;sliderWidth&quot;\:1060';
            let img = '\'\/bitrix\/components\/bitrix\/crm\.document\.view\/templates\/\.default\/images\/document_view\.svg\'';
            toolbarRight.prepend('<button onclick=\"BX\.DocumentGenerator\.Document\.onBeforeCreate(' + link + ', {' +  qq + '}, ' + img + ', \'crm\')" class="loadDoc ui-btn ui-btn-light-border --air ui-btn-no-caps --style-outline ui-btn-sm">Загрузить счет</button>');
        }        
    });
});
