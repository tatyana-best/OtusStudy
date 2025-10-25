BX.ready(function() {
    BX.addCustomEvent("SidePanel.Slider:onLoad", function(p,q) {
        if ($('[name="PARAMS[CATEGORY_ID]"]').val() == 1) {
            $('.crm-entity-section.crm-entity-section-status-wrap').hide();
        }
    });
})
