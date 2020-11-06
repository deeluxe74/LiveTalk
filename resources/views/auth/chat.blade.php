@extends('layouts.app')
@section('content')
    <div class="align-center d-flex flex-column justify-content-center">
        <div>
            <div class="badge badge-info mt-1 mb-4">CHAT</div>
        </div>
        <div class="d-flex flex-row chatbox">
            <div class="bg-grey bloc1 flex-fill">
                <ul>
                    @foreach ($users as $user)
                        @if($user != Auth::user())
                            <li onclick="isFocus(event,{{ Auth::user() }})">{{ ucfirst($user->name) }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>   
            <div class="bg-white flex-fill bloc2">
                <div class="content-chat">
                    <div id="content-chat"></div>
                </div>
                <div class="input-chat">
                    <textarea id="textArea" name="content" cols="0" rows="auto"></textarea>
                    <svg onclick="sendMessage( {{ Auth::user() }})" class="arrow-send" id="Capa_1" enable-background="new 0 0 512.004 512.004" height="30" viewBox="0 0 512.004 512.004" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m511.35 52.881-122 400c-3.044 9.919-14.974 13.828-23.29 7.67-7.717-5.727-203.749-151.217-214.37-159.1l-142.1-54.96c-5.79-2.24-9.6-7.81-9.59-14.02.01-6.21 3.85-11.77 9.65-13.98l482-184c5.824-2.232 12.488-.626 16.67 4.17 3.37 3.87 4.55 9.24 3.03 14.22z" fill="#94dfda"/><path d="m511.35 52.881-122 400c-3.044 9.919-14.974 13.828-23.29 7.67l-190.05-141.05 332.31-280.84c3.37 3.87 4.55 9.24 3.03 14.22z" fill="#61a7c5"/><path d="m507.89 58.821-271.49 286.4-63 125.03c-3.16 6.246-10.188 9.453-16.87 7.84-6.76-1.6-11.53-7.64-11.53-14.59v-175.3c0-4.86 2.35-9.41 6.31-12.23l337-239.69c6.29-4.48 14.95-3.45 20.01 2.38 5.07 5.83 4.88 14.56-.43 20.16z" fill="#eef4ff"/><path d="m507.89 58.821-271.49 286.4-63 125.03c-3.16 6.246-10.188 9.453-16.87 7.84-6.76-1.6-11.53-7.64-11.53-14.59l31.01-144 332.31-280.84c5.07 5.83 4.88 14.56-.43 20.16z" fill="#d9e6fc"/></g></svg>
                </div>
            </div>   
        </div>    
    </div>       
@endsection
<script>
    let destinataire = '';
    let arraySort = [];
    let intervalUpdate ;
    
    function isFocus(event, userCurr){
        clearInterval(intervalUpdate);
        const clearMessage = document.getElementById('content-chat');
        clearMessage.innerHTML = '';

        if(event.target.className == ''){
            updateMessage(event, userCurr);
        }else {
            event.target.className = "";
        }
    }

    function sendMessage(user){
        let content = document.getElementById("textArea").value;
        axios.post('api/chat/sendMessage', {
            content: content,
            destinataire: destinataire.toLocaleLowerCase(),
            userId: user.id
        }).then((response) => {
            document.getElementById("textArea").value = "";
        })
    }

    function updateMessage(event, userCurr){
        let elements= document.querySelectorAll('.focus');
            elements.forEach((el) => {
                el.classList.remove('focus');
            });
            destinataire = event.target.innerHTML.toLowerCase();
            event.target.className = "focus";

            axios.post(`api/chat/message`, {
                destinataire : destinataire,
                user_curr : userCurr.id
            }).then((response) => {
                const arrayMessages = response.data[0];
                const arrayMessagesOther = response.data[1];
                let AllMessage = arrayMessages.concat(arrayMessagesOther);
                arraySort = AllMessage.sort(function (a, b) { return a.created_at.localeCompare(b.created_at); });
                arraySort.forEach(message => {
                    const p = document.createElement("p");

                    if(message.from == userCurr.id){
                        p.className ="message-me";
                    }else {
                        p.className ="message-other";
                    }
                    const txt = document.createTextNode(message.content);
                    p.appendChild(txt);
                    const element = document.getElementById("content-chat");
                    element.appendChild(p);
                });
                intervalUpdate = setInterval(() => {
                    updateMessageEachTime(userCurr);  
                }, 2000);
            })
    }

    function updateMessageEachTime(userCurr){
        axios.post(`api/chat/updateMessage`, {
            allMessage : arraySort,
            destinataire : destinataire,
            user_curr : userCurr.id
        }).then((response) => {
            let success = false;
            let newMessage = response.data[0].concat(response.data[1]);       
            let messages = Array.from(new Set(newMessage.map(a => a.id)))
                .map(id => {
                return newMessage.find(a => a.id === id)
            })

            messages.forEach(message => {
                success = false;
                arraySort.forEach(element => {
                    if(element.id === message.id){
                        success = true;
                    }
                });
                if(!success){
                    let newMessageSend = message;
                    const p = document.createElement("p");
                    if(newMessageSend.from == userCurr.id){
                        p.className ="message-me";
                    }else {
                        p.className ="message-other";
                    }
                    const newtxt = document.createTextNode(newMessageSend.content);
                    p.appendChild(newtxt);
                    const element = document.getElementById("content-chat");
                    element.appendChild(p);

                    arraySort.push(newMessageSend);
                }
            });
        })
    }
</script>

