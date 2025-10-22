$(document).ready(function(){
    function showRow($el, $status)
    {
        $($el + $status).show();
    }

    function hideRow($el, $status)
    {
        $($el + $status).hide();
    }

    function rowHideShow($el, $check)
    {
        if ($check.is(':checked')) {
            showRow($el, $check.val());
        } else {
            hideRow($el, $check.val());
        }
    }

    $('[name^="status"]').attr('checked', 'checked');

    $('[name^="status"]').on('change', function() {
        let el = 'table.deals-with-filter tbody tr.';
        rowHideShow(el, $(this));
    });

})