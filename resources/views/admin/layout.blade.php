<!DOCTYPE html>
<html id="top">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=3.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Admin Panel</title>

    <link rel="icon" href="{{ asset('img/Logo.svg') }}" type="image/icon type">

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/style.css')}}">
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/css/bootstrap-datetimepicker.min.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    {{-- SELECT2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link  rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link  rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.3/css/fixedHeader.dataTables.min.css">

    {{-- DATEPICKER --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.css">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    @yield('head')



        @yield('css_before')

    <style>
        a .link :link {
            color: blue;
        }
    </style>

</head>

<body>

<a id="goTop"></a>

<div class="wrapper">
    <!-- Sidebar  -->
    <nav id="sidebar">
        <div class="sidebar-header text-center">
            <a href="{{route('index')}}">
            <img class="mb-4" src="{{ asset('img/Logo.svg') }}" alt="" width="100" height="100"></a>
        </div>

        <ul class="list-unstyled components">

            <li>
                <a href="{{route('user.index')}}">Find User <i class="fas fa-search"></i></a>
            </li>
            <li>
                <a href="#subMenuRookies" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" id="rookie-btn">Rookies</a>
                <ul class="collapse list-unstyled" id="subMenuRookies">
                    <li>
                        <a href="{{route('rookies.get')}}">All accounts</a>
                    </li>
                    <li>
                        <a href="{{route('rookies.id_verification')}}">ID Verification</a>
                    </li>
                    <li>
                        <a href="{{route('rookies.pending')}}">Pending accounts</a>
                    </li>
                    <li>
                        <a href="{{route('rookies.new_accounts')}}">New accounts</a>
                    </li>
                    <li>
                        <a href="{{route('rookies.updated_accounts')}}">Updated accounts</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{route('leaders.get')}}">Leaders</a>
            </li>
            <li>
                <a href="{{route('transaction.search')}}">Find Transaction <i class="fas fa-search"></i></a>
            </li>
            <li>
                <a href="#subCompliance" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" id="compliance-btn">Compliance</a>
                <ul class="collapse list-unstyled" id="subCompliance">
                    <li>
                        <a href="{{route('three_transactions')}}">1-3 Transactions</a>
                    </li>
                    <li>
                        <a href="{{route('transactions_refunds')}}">Transactions Refunds</a>
                    </li>
                    <li>
                        <a href="{{route('reports.daily')}}">Reports</a>
                    </li>
                    <li>
                        <a href="{{route('refunds.pending')}}">Refunds</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#subMenuPayment" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" id="payments-btn">Payments</a>
                <ul class="collapse list-unstyled" id="subMenuPayment">

                    <li>
                        <a href="{{route('main-payment')}}">MAIN PAYMENT REPORT</a>
                    </li>
                    <li>
                        <a href="{{route('user-payment-prev')}}">PAYMENTS FOR PREV. PERIOD REPORT</a>
                    </li>
                    <li>
                        <a href="{{route('summary-payment')}}">PERIOD SUMMARY HISTORY REPORT</a>
                    </li>
                    <li>
                        <a href="{{route('rejects_reports')}}">REJECTS REPORT</a>
                    </li>
                    <li>
                        <a href="{{route('user-payment-history')}}">PAYMENT HISTORY REPORT</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{route('complaints.get')}}">User Complaints</a>
            </li>
            <li>
                <a href="{{route('show.rookies_ofd')}}">Rookie Winners</a>
            </li>
            <li>
                <a href="{{route('goals.index')}}">Goals</a>
            </li>
            <li>
                <a href="{{route('content_editor')}}">Content Editor</a>
            </li>
            <li>
                <a href="{{route('bad.words.get')}}">Bad words</a>
            </li>
            <li>
                <a href="{{route('showMicromorgiBonus.get')}}">Micro morgi bonus</a>
            </li>
            <li>
                <a href="{{route('pubnub-channels-settings.get.index')}}">Channels Settings</a>
            </li>
        </ul>

        <ul class="list-unstyled CTAs">
            <li>
                <a class="nav-link" href="/logout">Logout</a>
            </li>
            <li>

            </li>
        </ul>
    </nav>

    <!-- Page Content  -->
    <div id="content">

        <button type="button" id="sidebarCollapse" class="btn btn-info">
            <i class="fas fa-align-left"></i>
        </button>
        <a type="button" id="returnBack" class="btn btn-light" href="#" onclick="history.back()">
            Go Back
        </a>
        <br>
        <br>
        @if(\Illuminate\Support\Facades\Session::has('fail'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>FAIL: </strong>
                @if(is_array(\Illuminate\Support\Facades\Session::get('fail')))
                    @foreach(\Illuminate\Support\Facades\Session::get('fail') as $k)
                        {{$k[0]}} <br>
                    @endforeach
                @else
                    {{\Illuminate\Support\Facades\Session::get('fail')}}
                @endif
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @elseif(\Illuminate\Support\Facades\Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>SUCCESS: </strong> {{ \Illuminate\Support\Facades\Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @elseif(\Illuminate\Support\Facades\Session::has('message'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>INFO: </strong> {{ \Illuminate\Support\Facades\Session::get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="alert alert-danger alert-dismissible fade" role="alert" id="alert-danger">
            <strong>FAIL: </strong><span id="alert-message"></span>
        </div>

        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert" style="display: none">
            <strong>SUCCESS: </strong> <label id="success-alert-message"></label>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert" style="display: none">
            <strong>ERROR: </strong> <label id="error-alert-message"></label>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="container-lg">

            @yield('content')
        </div>

    </div>
</div>

<!-- jQuery CDN - Slim version (=without AJAX) -->
{{--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>--}}

<!-- jQuery -->

<!-- Popper.JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
<!-- jQuery Custom Scroller CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
<!-- https://datatables.net/ -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" ></script>

<script type="text/javascript" src="{{asset('js/layout.js')}}"></script>

<link href="https://fonts.googleapis.com/css?family=Merriweather:400,900,900i" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

@yield('js_after')

<script>
    var btn = $('#goTop');

    $(window).scroll(function() {
        if ($(window).scrollTop() > 100) {
            btn.addClass('show');
        } else {
            btn.removeClass('show');
        }
    });

    btn.on('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    $(document).ready(function () {
        $("#sidebar").mCustomScrollbar({
            theme: "minimal"
        });

        $('#sidebarCollapse').on('click', function () {
            $('#sidebar, #content').toggleClass('active');
            $('.collapse.in').toggleClass('in');
            $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        });

        let url = window.location.href;
        if (url.includes('rookie/payments')){
            $('#payments-btn').attr('aria-expanded', "true");
            $('#subMenuPayment').addClass("show");
        }

        if (url.includes('/compliance/') && !url.includes('/compliance/transactions/')){
            $('#compliance-btn').attr('aria-expanded', "true");
            $('#subCompliance').addClass("show");
        }

        if (url.includes('/rookies/')){
            $('#rookie-btn').attr('aria-expanded', "true");
            $('#subMenuRookies').addClass("show");
        }

    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(".enable-select2").select2({
        tags: true,
        width: '100%',
        maximumInputLength: {{env('MAX_INPUT_FREE_TEXT', 120)}},
    }).attr('maxlength', 5);
</script>
</body>

</html>
