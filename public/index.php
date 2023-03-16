<?php

require_once '../vendor/autoload.php';

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="//cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="css/details.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">

</div>

<!-- JQUERY -->
<script
    src="https://code.jquery.com/jquery-3.6.0.slim.min.js"
    integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI="
    crossorigin="anonymous"></script>

<!-- DATATABLES -->
<script src="//cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>


<!-- Bootstrap and Datatables Bootstrap theme (OPTIONAL) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

<script type="text/javascript">
    ( function ($) {

        $.extend(true, $.fn.dataTable.defaults, {
            paging:  false, // no paging
            column: {
                orderSequence: ['desc','asc']
            }
        });

        // https://datatables.net/
        $('.datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
            ]
        });
    })(jQuery);
</script>

</body>
</html>
