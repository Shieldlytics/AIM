<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/fontawesome-pro-5.15.4/css/all.css">
        <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>By Risk Level</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button id="" class="btn btn-secondary">View All Data</button>
                    <button id="Top" class="btn btn-danger">Top</button>
                    <button id="High" class="btn btn-warning">High</button>
                    <button id="Moderate" class="btn btn-primary">Moderate</button>
                    <button id="Low" class="btn btn-warning">Low</button>
                </div> 
            </div>
            <div class="row">
                <div class="col-12">
                    <div id="results"></div>
                </div>
            </div>
                

        </div>
        
        <script src="assets/js/app.js"></script>
        <script src="node_modules/jquery/dist/jquery.js"></script>
        <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    </body>
</html>
<script>
    $(document).ready(function(){
        //loadAllData();

        $('.btn').click(function(){
            var rl = $(this).attr('id');
            loadHighData (rl);
        });
    });

    function loadAllData(){
        $.ajax({
            url: 'assets/PHP/functions.php',
            dataType: 'json',
            type: 'GET',
            data: {method: 'getAll'},
            success: function(data){
                console.log(data);
            }
        });
    }
    function loadTopData(){
        $.ajax({
            url: 'assets/PHP/functions.php',
            dataType: 'json',
            type: 'GET',
            data: {method: 'getTop'},
            success: function(data){
                console.log(data);
            }
        });
    }
    function loadHighData (rl){
        $.ajax({
            url: 'assets/PHP/functions.php',
            dataType: 'json',
            type: 'GET',
            data: {
                method: 'getByRiskLevel', 
                riskLevel: rl
            },
            success: function(data){
                $('#result').html(data.items);
            },
            error: function(xhr, status, error) {
                // Handle error
                $('#result').html("Error: " + error);
            }
        });
    }
</script>