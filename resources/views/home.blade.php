@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3 chat-sidebar">
            <div class="users">
                <h5>Users</h5>
                <ul class="list-group list-chat-item">
                    @if($users->count())
                        @foreach($users as $user)
                            <li class="chat-user-list">
                                <a href="{{ route("message.conversation", $user->id) }}">
                                    <div class="chat-image">
                                        {!! makeImageFromName($user->name) !!}
                                        <i class="fa fa-circle user-status-icon user-icon-{{ $user->id }}" title="away"></i>
                                    </div>
                                    <div class="chat-name font-weight-bold">
                                        {{ $user->name }}
                                    </div>

                                </a>
                            </li>
                        @endforeach
                   @endif
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <h1>
                Message Section
            </h1>

            <p class="lead">
                Select user from the list to begin conversation.
            </p>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        $(function(){
            let user_id = '{{ auth()->id() }}';
            let ip_address = 'chat.test';
            let socket_port = '3000';
            let socket =    io(ip_address + ':' + socket_port, { transport : ['websocket'] });

            socket.on('connect', () => {
                socket.emit('user_connected', user_id);
            });

            socket.on('updateUserStatus', (data) => {
                let $userStatusIcon = $('.user-status-icon');
                $userStatusIcon.removeClass('text-success');
                $userStatusIcon.attr('title', 'Away');

                $.each(data, (key, val) => {
                     if(val !== null && val !== 0 && user_id !== ''){
                     //   console.log(key);
                        let $userIcon = $(".user-icon-" + key);
                        $userIcon.addClass('text-success');
                        $userIcon.attr('title', 'Online');
                    }
                });
            });

        });
    </script>
@endpush
