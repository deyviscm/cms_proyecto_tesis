<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <title>OMEGA</title>
        <!-- CSRF Token -->
        <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="stylesheet" href="{{ url(mix('/css/app.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/dashforge.css') }}">
    </head>
    <body>
        <div id="app"></div>
        <!-- App js -->
        
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="{{ asset('js/dashforge.js') }}">
        <script src="{{ url(mix('/js/app.js')) }}" type="text/javascript"></script>
    </body>
    <script>
        // function saveAS(uri, fileName){
        //     var link = document.createElement('a');
        //     if(typeof link.download === 'string'){
        //         document.body.appendChild(link);
        //         link.download = filename;
        //         link.href = uri;
        //         link.click();
        //         document.body.removeChild(link);
        //     }else{
        //         location.replace(uri)
        //     }
        // }
    </script>
</html>