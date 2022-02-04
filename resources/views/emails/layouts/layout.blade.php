<!DOCTYPE html>
<html>
    <head>        
        <title>@yield('title', env("APP_NAME"))</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	    
	    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>        
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@500&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Raleway', sans-serif;
                font-weight: 500;
            }
            p {
                font-size: 15px;
                color: #626262;
                line-height: 26px;
            }
            h1 {
                font-size: 30px;
                text-transform: capitalize;
                margin-top: 30px;
                margin-bottom: 30px;
                color: #3da750;
                font-weight: 500;
            }
        </style>
    </head>
    <body style="max-width: 800px;margin: 0 auto;">
        <div class="main_section" style="border-top: 6px solid {{ $details['theme_color'] }};">
            <div class="banner_section" style="text-align: center;">
                <div class="banner_img" style="background-image: url({{ $details['header_img'] }});height: 340px;width: 100%;background-repeat: no-repeat;background-size: contain;margin-top: -30px;"></div>
            </div>
            <div class="main_content" style="padding: 0 50px;">
                @yield('content')
            </div>
        </div>
        <div class="footer_section" style="margin-top: 80px;background-color: {{ $details['theme_color'] }};color: #fff;border-top-left-radius: 30px;border-top-right-radius: 30px;justify-content: space-between;padding: 18px 50px;align-items: center;">
            {!! $details['footer'] !!}
        </div>
    </body>
</html>