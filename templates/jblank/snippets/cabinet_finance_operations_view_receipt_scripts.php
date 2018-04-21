<script>
    function getReceiptOperation(operationID, type, template){
        (function ($) {
            $("#get_receipt_operation_modal .preloader_block").removeClass("preloader_complete");
            $("#get_receipt_operation_modal").modal();

            $.ajax({
                url: '/index.php?AJAX&TEMPLATE=cabinet_remittance&FinancialOperationType='+type,
                method: "post",
                data: {data: {operationId: operationID}, SERVICE: 'receipt'},
                dataType: "html",
                success: function(r){
                    if (r != "exception") {
                        $("#get_receipt_operation_modal .preloader_content").html(r);

                        setTimeout(function () {
                            $("#get_receipt_operation_modal .preloader_block").addClass("preloader_complete");
                        }, 300);
                    } else oops();


                }
            });


        }(jQuery))
    }

    function getReceiptOperationDef(ser) {
        (function ($) {
            $("#get_receipt_operation_modal .preloader_block").removeClass("preloader_complete");
            $("#get_receipt_operation_modal").modal();

            $.ajax({
                url: '/index.php?AJAX&TEMPLATE=cabinet_remittance_receipt_def',
                method: "post",
                data: {data: ser},
                dataType: "html",
                success: function(r){
                    $("#get_receipt_operation_modal .preloader_content").html(r);

                    setTimeout(function () {
                        $("#get_receipt_operation_modal .preloader_block").addClass("preloader_complete");
                    }, 300);
                }
            });


        }(jQuery))
    }
</script>
