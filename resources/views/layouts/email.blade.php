<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>A to Z Email</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto:400,500,700');

        body {
            font-family: 'Roboto', sans-serif;
            color: black;
        }

        .main-table {
            padding: 20px 15px 25px;
        }

        .footer-table {
            padding: 20px 15px 8px;
        }

        .footer-img {
            max-width: 23px;
        }

        .footer-icons {
            text-align: left;
        }

        tr td {
            display: block;
        }

        .footer-table td {
            margin-bottom: 15px;
        }

        @media (min-width: 768px) {
            .main-table {
                padding: 40px 38px 36px;
            }

            .footer-table {
                padding: 34px 38px 23px;
            }

            tr td {
                display: table-cell;
            }

            .footer-img {
                max-width: 26px;
            }

            .footer-icons {
                text-align: right;
            }
        }
    </style>
</head>
<body style="background-color: #f9f9f9;font-family: 'Roboto', sans-serif;">
    <table style="width: 100%; max-width: 660px; margin: auto; border-collapse: collapse;font-family: 'Roboto', sans-serif;">
        <thead>
            <th>
                <img src="{{ asset('img/a-to-z-logo.png') }}" alt="A to Z" style="display: block; margin: 23px auto 27px; width: 100%; max-width: 300px;">
            </th>
        </thead>
        <tbody>
            <tr>
                <td>
                    <table class="main-table" style="background-color: #fff;width:100%">
                        @yield('content')
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="footer-table" style="background-color: #000; padding: 34px 38px 23px; width: 100%;">
                        <tr>
                            <td valign="top">
                                <p style="margin: 0; color: #fff; font-size: 12px; line-height: 17px; font-weight: bold">A to Z Interpreting</p>
                                <p style="margin: 0; color: #fff; font-size: 12px; line-height: 17px;">15 Wellington Road,</p>
                                <p style="margin: 0; color: #fff; font-size: 12px; line-height: 17px;">Edgbaston,</p>
                                <p style="margin: 0; color: #fff; font-size: 12px; line-height: 17px;">Birmingham</p>
                                <p style="margin: 0; color: #fff; font-size: 12px; line-height: 17px;">B15 2EU</p>
                            </td>
                            <td>
                                <a style="margin: 0; color: #fff; font-size: 12px; line-height: 17px; text-decoration: none;" href="tel:01212491933">Tel: 0121 249 1933</a><br>
                                <a style="margin: 0; color: #fff; font-size: 12px; line-height: 17px; text-decoration: none;" href="fax:01212582204">Fax: 0121 258 2204</a><br>
                                <a style="margin: 0; color: #fff; font-size: 12px; line-height: 17px; text-decoration: none;" href="skype:atozinterpreting?call">Skype:atozinterpreting</a><br>
                                <a style="margin: 0; color: #fff; font-size: 12px; line-height: 17px; text-decoration: none;" href="tel:07739637807">
                                    Out-of-Hours: 07739 637 807
                                </a><br>
                                <a style="margin: 0; color: #fff; font-size: 12px; line-height: 17px; text-decoration: none;" href="mailto:enquiries@atozinterpreting.com">enquiries@atozinterpreting.com</a>
                            </td>
                            <td class="footer-icons" valign="top">
                                <a style="text-decoration: none;" target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/AtoZ-Interpreting-and-Translation-Services-135717486482411/">
                                    <img class="footer-img" src="{{ asset('img/social/facebook.png') }}" alt="Facebook">
                                </a>
                                <a style="text-decoration: none; margin-left: 6px;" target="_blank" rel="noopener noreferrer" href="https://twitter.com/a2zinterpreting">
                                    <img class="footer-img" src="{{ asset('img/social/twitter.png') }}" alt="Twitter">
                                </a>
                                <a style="text-decoration: none; margin-left: 6px;" target="_blank" rel="noopener noreferrer" href="http://www.linkedin.com/company/a-to-z-interpreting-&-translation-services-limited/about">
                                    <img class="footer-img" src="{{ asset('img/social/linkedin.png') }}" alt="Linkedin">
                                </a>
                                <a style="text-decoration: none; margin-left: 6px;" target="_blank" rel="noopener noreferrer" href="http://atozinterpreting.com/blog/?feed=rss2">
                                    <img class="footer-img" src="{{ asset('img/social/blog.png') }}" alt="Blog">
                                </a>
                                <a style="text-decoration: none; margin-left: 6px;" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/user/atozinterpreting">
                                    <img class="footer-img" src="{{ asset('img/social/youtube.png') }}" alt="Youtube">
                                </a>
                                <a style="text-decoration: none; margin-left: 6px;" target="_blank" rel="noopener noreferrer" href="skype:atozinterpreting?call">
                                    <img class="footer-img" src="{{ asset('img/social/skype.png') }}" alt="Skype">
                                </a>
                            </td>
                        </tr>
                    </table>
                    <p style="text-align:center;padding-top: 16px;padding-bottom: 35px;font-style: italic;font-weight: 500;font-size: 12px;line-height: 17px;color: #4A4A4A;margin:0;background-color:white">
                        Please do not reply to this email. This email address is not monitored. If you would like to contact us, please send your email to <a style="text-decoration: none;font-style: italic;color: #FBA528;font-size: 12px;line-height: 17px;" href="mailto:bookings@atozinterpreting.com">bookings@atozinterpreting.com</a> 
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>