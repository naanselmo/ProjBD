$(function () {
    /* Dynamic selector API */
    $('.dynamic-selector-list').change(function (event) {
        // Find the .dynamic-selector-list who triggered the change.
        var $target = $(event.target);
        // Find the .dynamic-selector which is the parent of the triggered .dynamic-selector-list.
        var $dynamicSelector = $target.closest('.dynamic-selector');
        // Go through the infos and try to match their selected value with the one selected.
        $dynamicSelector.children('.dynamic-selector-info').hide().filter(function () {
            return $(this).data('value') == $target.val();
        }).show();
    }).trigger('change');


    /* Offer.php rentable selector */
    $('#rentable').change(function (event) {
        var $target = $(event.target);
        var $option = $target.find(':selected');
        var address = $option.data('address');
        var code = $option.data('code');
        var $form = $target.closest('form');
        $form.find('input[name="address"]').val(address);
        $form.find('input[name="code"]').val(code);
    }).trigger('change');

    /* Make all tables sortable! */
    $('.tablesorter').each(function () {
        var headers = {};
        var $table = $(this);
        $table.find('.no-sort').each(function () {
            headers["" + $(this).index()] = {sorter: false};
        });
        $table.tablesorter({headers: headers});
    });
});
