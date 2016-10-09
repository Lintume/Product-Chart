<html>
<head>
    <meta charset="utf-8">
    <title>Connection Type</title>
</head>
<body>
    <div style="margin-left: 1%; margin-top: 1%">
        <p><b><h2>Connection Type</h2></b></p>
        <p><label class = "local">
                <input name="connection_type" type="radio" value="local">
                Local
            </label></p>
        <p><label class = "remote">
                <input name="connection_type" type="radio" value="remote">
                Remote
            </label></p>

        <div id = "myForm">
            <form style="width: 40%" class="form-horizontal hidden">
                <div class="form-group">
                    <label class="control-label col-sm-4" for="host">Host:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="host" placeholder="Enter host">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4" for="database">Database:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="database" placeholder="Enter database">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4" for="user">User:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="user" placeholder="Enter user">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-4" for="pwd">Password:</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" id="pwd" placeholder="Enter password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </div>
            </form>
            <label class="hidden dataSaved">
                Data saved!
            </label>
        </div>
    </div>
</body>
</html>

<?php
add_action('admin_print_footer_scripts', 'my_action_javascript', 99);
function my_action_javascript() {
    ?>
    <script type="text/javascript" >
        $(document).ready(function(){
            $('.remote').change(function(){
                $('.form-horizontal').removeClass('hidden');
            });

            $('.local').change(function(){
                var data= {action: 'my_action_local'};
                $.post( ajaxurl, data, function(response) {
                    if (response == 'true')
                    {
                        $('.dataSaved').removeClass('hidden');
                    }
                });
            });

            $("#myForm form").submit(function(event)//pressing event type Submit
            {
                event.preventDefault();//disable the default behavior
                var data= { action: 'my_action_remote',host: $('#host').val(), database: $('#database').val(), user: $('#user').val(), pwd: $('#pwd').val()};
                $.post( ajaxurl, data, function(response) {
                    if (response == 'true')
                    {
                        $('.dataSaved').removeClass('hidden');
                    }
                });
            });
        });
    </script>
    <?php
}
    ?>