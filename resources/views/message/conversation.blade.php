@extends('layouts.app')

@section('content')
    <div class="row chat-row">
        <div class="col-md-3 chat-sidebar">
            <div class="users">
                <h5>Users</h5>
                <ul class="list-group list-chat-item">
                    @if($users->count())
                        @foreach($users as $user)
                            <li class="chat-user-list"
                                @if($user->id == $friendInfo->id) active @endif>
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
        <div class="col-md-9 chat-section">
          <div class="chat-header">
              <div class="chat-image">
                  {!! makeImageFromName($friendInfo->name) !!}
                  <i class="fa fa-circle user-status-head" title="away"
                     id="userStatusHead{{ $friendInfo->id }}"></i>
              </div>
              <div class="chat-name font-weight-bold">
                  {{ $friendInfo->name }}
              </div>
          </div>

            <div class="chat-body" id="chatBody">
                <div class="message-listing" id="messageWrapper">
                    <div class="row message align-items-center mb-2">

                    </div>
                </div>
            </div>

            <div class="chat-box">
                <div class="chat-input bg-white" id="chatInput" contenteditable="">

                </div>

                <div class="chat-input-toolbar">
                    <button title="Add File" class="btn btn-light btn-sm btn-file-upload">
                        <i class="fa fa-paperclip"></i>
                    </button> |

                    <button title="Bold" class="btn btn-light btn-sm tool-items"
                            onclick="document.execCommand('bold', false, '');">
                        <i class="fa fa-bold tool-icon"></i>
                    </button> |

                    <button title="Italic" class="btn btn-light btn-sm tool-items"
                            onclick="document.execCommand('italic', false, '');">
                        <i class="fa fa-italic tool-icon"></i>
                    </button>


                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        $(function(){

            let $chatInput = $(".chat-input");
            let $messageWrapper = $("#messageWrapper");

            let user_id = '{{ auth()->id() }}';
            let ip_address = 'chat.test';
            let socket_port = '3000';
            let socket =    io(ip_address + ':' + socket_port, { transport : ['websocket'] });
            let friendId = "{{ $friendInfo->id }}";

            socket.on('connect', () => {
                socket.emit('user_connected', user_id);
            });

            socket.on('updateUserStatus', (data) => {

                let $userStatusIcon = $('.user-status-icon');
                $userStatusIcon.removeClass('text-success');
                $userStatusIcon.attr('title', 'Away');

                $.each(data, (key, val) => {
                    if(val !== null && val !== 0){
                        console.log(key);
                        let $userIcon = $(".user-icon-" + key);
                        $userIcon.addClass('text-success');
                        $userIcon.attr('title', 'Online');
                    }
                });
            });

            $chatInput.keypress(function(e){
                let message = $(this).html();
                if(e.which === 13 && !e.shiftKey){
                    $chatInput.html("");
                    sendMessage(message);
                    return false;
                }
            });

            function sendMessage(message){
                let url = "{{ route("message.send-message") }}";
                let formData = new FormData();
                let token = "{{ csrf_token() }}";

                formData.append('message', message);
                formData.append('_token', token);
                formData.append('receiver_id', friendId);

                appendMessageToSender(message);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'JSON',
                    success: function(response){
                        if(response.success){
                            console.log(response.data);
                        }
                    }
                });
            }

            function appendMessageToSender(message){
                let name = '{{ $myInfo->name }}';
                let image = '{!! makeImageFromName($myInfo->name) !!}';

                let userInfo = '<div class="col-md-12 user-info">\n' +
                    '<div class="chat-image">\n' +
                    image +'\n' +
                    '</div>\n' +
                    '<div class="chat-name font-weight-bold">\n' +
                    name +
                    '</div>\n' +
                    '\n' +
                    '<span class="small time text-gray-500" title="'+getCurrentDateTime()+'">\n' +
                    getCurrentTime() +'\n' +
                    '</span>\n' +
                    '</div>\n' +
                    '</div>';

                let  messageContent = '<div class="col-md-12 message-content">\n' +
                    '<div class="message-text">\n' +
                     message + '\n'+
                    '</div>\n' +
                    '</div>';

                $newMessage = '<div class="row message align-items-center mb-2">' + userInfo + messageContent + '</div>';

                $messageWrapper.append($newMessage);
            }

            function appendMessageToReceiver(message){
                let name = '{{ $friendInfo->name }}';
                let image = '{!! makeImageFromName($friendInfo->name) !!}';

                let userInfo = '<div class="col-md-12 user-info">\n' +
                    '<div class="chat-image">\n' +
                    image +'\n' +
                    '</div>\n' +
                    '<div class="chat-name font-weight-bold">\n' +
                    name +
                    '</div>\n' +
                    '\n' +
                    '<span class="small time text-gray-500" title="'+dateFormat(message.created_at)+'">\n' +
                    timeFormat(message.created_at) +'\n' +
                    '</span>\n' +
                    '</div>\n' +
                    '</div>';

                let  messageContent = '<div class="col-md-12 message-content">\n' +
                    '<div class="message-text">\n' +
                    message.content + '\n'+
                    '</div>\n' +
                    '</div>';

                $newMessage = '<div class="row message align-items-center mb-2">' + userInfo + messageContent + '</div>';

                $messageWrapper.append($newMessage);
            }

            socket.on("private-channel:App\\Events\\PrivateMessageEvent", function(message){
                appendMessageToReceiver(message);
            });

        });
    </script>
@endpush
