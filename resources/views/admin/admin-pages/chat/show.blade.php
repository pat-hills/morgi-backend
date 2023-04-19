<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=3.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Admin Panel</title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
          integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/chat/style.css')}}">

    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <!-- https://datatables.net/ -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js"
            integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ"
            crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js"
            integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY"
            crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


</head>

<body>
<div id="loading">
</div>
<div id="content-messages">
    <nav class="navbar navbar-expand fixed-top navbar-light bg-light" id="navbar_id">
        <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item mr-2">
                    @if($user_reported->type == 'leader')
                        <img src="{{$user_reported->getOwnAvatar()->url ?? asset('img/noAvatar.png')}}" class="rounded-circle">
                    @else
                        <img src="{{$user_reported_by->getOwnAvatar()->url ?? asset('img/noAvatar.png')}}" class="rounded-circle">
                    @endif
                </li>

                <li class="nav-item">
                    @if($user_reported->type == 'leader')
                        <h5>{{$user_reported->username}}</h5>
                        <small><em>{{ucfirst($user_reported->type)}}</em></small>
                        <br>
                        <small><em>Last login {{date('Y-m-d H:i', strtotime($user_reported->last_login_at))}}</em></small>
                    @else
                        <h5>{{$user_reported_by->username}}</h5>
                        <small><em>{{ucfirst($user_reported_by->type)}}</em></small>
                        <br>
                        <small><em>Last login {{date('Y-m-d H:i', strtotime($user_reported_by->last_login_at))}}</em></small>
                    @endif
                </li>
            </ul>
        </div>
        <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item mr-2 text-right">
                    @if($user_reported_by->type == 'rookie')
                        <h5>{{$user_reported_by->username}}</h5>
                        <small><em>{{ucfirst($user_reported_by->type)}}</em></small>
                        <br>
                        <small><em>Last login {{date('Y-m-d H:i', strtotime($user_reported_by->last_login_at))}}</em></small>
                    @else
                        <h5>{{$user_reported->username}}</h5>
                        <small><em>{{ucfirst($user_reported->type)}}</em></small>
                        <br>
                        <small><em>Last login {{date('Y-m-d H:i', strtotime($user_reported->last_login_at))}}</em></small>
                    @endif
                </li>
                <li class="nav-item">
                    @if($user_reported_by->type == 'rookie')
                        <img src="{{$user_reported_by->getOwnAvatar()->url ?? asset('img/noAvatar.png')}}" class="rounded-circle">
                    @else
                        <img src="{{$user_reported->getOwnAvatar()->url ?? asset('img/noAvatar.png')}}" class="rounded-circle">
                    @endif
                </li>
            </ul>
        </div>
    </nav>



    <div class="wrap-content" id="wra">
        <div class="messages" id="chatBox">
            <div class="row" style="justify-content: center;">
                <!--Loading ANIMATION-->
                <img id="loader"
                     src='https://static.wixstatic.com/media/b33c56_ebc60fbe269f43868915a213e4c882fb~mv2.gif' alt="">
                <!--END LOADING ANIMATION-->
            </div>

            <div class='inner'>

                <ul id="ul">

                </ul>
                <a id="goDown"><i class="fas fa-4x fa-angle-down" style="color: white"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="container" id="not_access" style="display: none">
    <div id="denied_message" class="row text-center">

    </div>
</div>


<script>
    function onReady(callback) {
        var intervalID = window.setInterval(checkReady, 3000);

        function checkReady() {
            if (document.getElementsByTagName('body')[0] !== undefined) {
                window.clearInterval(intervalID);
                callback.call(this);
            }
        }
    }

    function show(id, value) {
        document.getElementById(id).style.display = value ? 'block' : 'none';
    }

    onReady(function () {
        // show('content-messages', true);
        show('loading', false);
        chatbox = document.getElementById("chatBox");
        chatbox.scrollTop = chatbox.scrollHeight;
        // window.scrollTo(0,document.body.scrollHeight);
    });
</script>

<script>
    function scrollSmoothToBottom (id) {
        var div = document.getElementById(id);
        $('#' + id).animate({
            scrollTop: div.scrollHeight - div.clientHeight
        }, 500);
    }
</script>
<script>

    let sent_leader;
    let replies_rookie;

    @if($user_reported->type == 'leader')
        sent_leader = {{$user_reported->id}};
        replies_rookie = {{$user_reported_by->id}};

    @else
        sent_leader = {{$user_reported_by->id}};
        replies_rookie = {{$user_reported->id}};

    @endif


    let response;
    let array;
    let array_reversed;
    const link_micromorgi_svg = '{{asset('img/micromorgi.svg')}}';

    let who;

    $(document).ready(function () {
        $.ajax({
            url: '{{route('messages.get')}}',
            type: "GET",
            data: {
                "leader_id": sent_leader,
                "rookie_id": replies_rookie,
                "limit": 20,
            },
            dataType: 'json',
            success: function (data) {
                if (data.data.length === 0){
                    document.getElementById('ul').innerHTML = '<br><br><h5>No messages between users</h5>';
                    return;
                }
                array = data.data;

                array.forEach(function(element) {
                    if (element.user_id === sent_leader){
                        who = 'sent';
                    }else if ( element.user_id === replies_rookie) {
                        who = 'replies';
                    }else{
                        return;
                    }

                    if (element.type === 'message'){
                        $('.inner ul').prepend('<li class="'+who+'"><p>'+element.text+'<br><small>'+((element.sent_at === null) ? 'none' : element.sent_at)+'</small></p></li>');
                    }else if (element.type === 'photo'){
                        $('.inner ul').prepend('<li class="'+who+'"><p class="attachment"><img src="'+element.url+'" alt=""/><br><small>'+((element.sent_at === null) ? 'none' : element.sent_at)+'</small></p></li>');
                    }else if (element.type === 'video'){
                        $('.inner ul').prepend('<li class="'+who+'"> <p class="attachment"><video controls id="vid_to_show"><source src="'+element.url+'" type="video/mp4"></video><br><small>'+((element.sent_at === null) ? 'none' : element.sent_at)+'</small></p></li>');
                    }else if (element.type === 'micromorgi_transaction'){
                        $('.inner ul').prepend('<li class="'+who+'"> <p class="attachment"><img src="'+link_micromorgi_svg+'"/> + '+ element.micromorgi_amount +'<br><small>'+((element.sent_at === null) ? 'none' : element.sent_at)+'</small></p></li>');
                    }

                });

                response = data;

            },
            error: function (data) {
                response = data;
                document.getElementById("content-messages").style.display = "none";
                document.getElementById("not_access").style.display = "block";

                document.getElementById('denied_message').innerHTML = '<h5>' + data['responseJSON']['message'] + '</h5';

            }
        });

    });

    function scrollDown(el) {
        el.animate({
            scrollTop: el[0].scrollHeight
        }, 500, function () {
            scrollUp(el)
        });
    }

    $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);

    // Assign scroll function to chatBox DIV
    $('#chatBox').scroll(function () {

        let nextPage;
        if ($('#chatBox').scrollTop() == 0) {
            nextPage = response ? response['next_page_url'] : '';
            if (nextPage == null || nextPage === '') {
                return;
            }
            $('#loader').show();

            if(nextPage !== ''){
                setTimeout(function () {
                    loadMore(nextPage);

                    // Hide loader on success
                    $('#loader').hide();
                    // Reset scroll
                    $('#chatBox').scrollTop(30);
                }, 780);
            }
        }
    });


    function loadMore(nextPage) {
        $.ajax({
            url: nextPage,
            type: "GET",
            data: {
                "leader_id": sent_leader,
                "rookie_id": replies_rookie,
                "limit": 10,
            },
            dataType: 'json',
            success: function (data) {
                response = data;

                array = data.data;

                array.forEach(function(element) {
                    if (element.user_id === sent_leader){
                        who = 'sent';
                    }else if ( element.user_id === replies_rookie) {
                        who = 'replies';
                    }else{
                        return;
                    }

                    if (element.type === 'message'){
                        $('.inner ul').prepend('<li class="'+who+'"><p>'+element.text+'<br><small>'+((element.sent_at === null) ? 'none' : element.sent_at)+'</small></p></li>');
                    }else if (element.type === 'photo'){
                        $('.inner ul').prepend('<li class="'+who+'"><p class="attachment"><img src="'+element.url+'" alt=""/><br><small>'+((element.sent_at === null) ? 'none' : element.sent_at)+'</small></p></li>');
                    }else if (element.type === 'video'){
                        $('.inner ul').prepend('<li class="'+who+'"> <p class="attachment"><video controls id="vid_to_show"><source src="'+element.url+'" type="video/mp4"></video><br><small>'+((element.sent_at === null) ? 'none' : element.sent_at)+'</small></p></li>');
                    }else if (element.type === 'micromorgi_transaction'){
                        $('.inner ul').prepend('<li class="'+who+'"> <p class="attachment"><img src="'+link_micromorgi_svg+'"/> + '+ element.micromorgi_amount +'<br><small>'+((element.sent_at === null) ? 'none' : element.sent_at)+'</small></p></li>');
                    }

                });

            },
            error: function (data) {
                response = data;
                $('.inner ul').prepend('<br><br><h5>' + data['responseJSON']['message'] + '</h5>');
                console.log("Something went wrong! Result data below")
                console.log(data);
            }
        });


    }
</script>


<script>
    const goDown = $('#goDown');
    let current_scroll;
    let chatbox;

    $('#chatBox').scroll(function () {

        height = $("#chatBox")[0].scrollHeight;
        current_scroll = $("#chatBox").scrollTop();

        if(current_scroll < (height - 1000)){
            goDown.addClass('show');
        }else{
            goDown.removeClass('show');
        }
    });

    goDown.on('click', function (e) {

        chatbox = document.getElementById("chatBox");
        chatbox.scrollTop = chatbox.scrollHeight;

    });
</script>

</body>

</html>
