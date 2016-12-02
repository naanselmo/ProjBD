$('#paymentModal').on('show.bs.modal', function (event) {
    var link = $(event.relatedTarget);
    // Fetch the data from the link.
    var number = link.data('number');

    // Set the value of the hidden values in the form.
    var modal = $(this);
    modal.find('input[name="number"]').val(number);
});

$('#reserveModal').on('show.bs.modal', function (event) {
    var link = $(event.relatedTarget);
    // Fetch the data from the link.
    var address = link.data('address');
    var code = link.data('code');
    var startDate = link.data('start_date');

    // Set the value of the hidden values in the form.
    var modal = $(this);
    modal.find('input[name="address"]').val(address);
    modal.find('input[name="code"]').val(code);
    modal.find('input[name="start_date"]').val(startDate);
});