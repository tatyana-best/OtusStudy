BX.ready(function() {
    BX.addCustomEvent('BX.UI.EntityEditorField:onLayout', function(e) {
        var fieldFiles = ['UF_DOCUMENTS', 'UF_SOME', 'UF_GHGH'];
        var strFields = '';
        var arStr = [];
        $.each(fieldFiles, function(inf, valf){
            //console.log(inf + ': ' + valf);
            arStr[inf] = 'div[data-cid="' + valf + '"] .file';
        })
        strFields = arStr.join(', ');
        console.log(strFields);
        //$('div[data-cid="UF_DOCUMENTS"] label.ui-entity-editor-block-title-text').after('<a href="" class="download">Скачать архив</span>');
        $('div[data-cid="UF_DOCUMENTS"] .file').append('<a href="" class="download">Скачать архив</span>');
        $('a.download').click(function(e){
            e.preventDefault();
            let elemenetId = window.location.pathname.split('/')[4];
            const dealData = {
                id: elemenetId,
            };
            console.log(dealData.id);
            BX.ajax({
                url: '/local/ajax/downloadArchive.php',
                method: 'POST',
                dataType: 'json',
                data: dealData,
                onsuccess: function ($data) {
                    let getData = JSON.parse(JSON.stringify($data));
                    console.log(getData.message);
                    const link = document.createElement('a');
                    link.href = '/local/ajax/download.zip';
                    link.download = 'documents.zip';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    BX.ajax({
                        url: '/local/ajax/downloadArchive.php',
                        method: 'POST',
                        dataType: 'json',
                        data: {ok: 'ok'},
                        onsuccess: function ($data) {
                            let getData = JSON.parse(JSON.stringify($data));
                            console.log(getData.message);
                        },
                        onfailure: function ($data) {
                            console.error();
                        }
                    });
                },
                onfailure: function ($data) {
                    console.error();
                }
            });
        });        
    });    
});
