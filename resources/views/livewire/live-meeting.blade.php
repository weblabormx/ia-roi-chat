<div class="max-w-5xl mx-auto relative h-full">
    <div id="messagesContainer" class="space-y-4 overflow-y-auto overflow-x-hidden pr-4" style="height: calc(100vh - 280px)" wire:poll>
        @foreach($meeting->messages()->get() as $message)
            @if($message->role == 'user')
                <div class="bg-gray-500 p-4 my-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ $meeting->idea->user->initials() }}
                                </span>
                            </span>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ $meeting->idea->user->name }}</span>
                                <span class="truncate text-xs">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 space-y-4">
                        {!! Str::markdown($message->message) !!}
                    </div>
                </div>
            @elseif($message->role == 'error')
                <div class="text-red-300 text-sm">
                    {{ $message->message }}
                </div>
            @else
                <div class="space-y-4">
                    {!! Str::markdown($message->message) !!}
                </div>
            @endif
        @endforeach
    </div>
    @if(!$meeting->is_finished)
        <div class="bg-gray-700 p-8 absolute" style="bottom: 0; left:0; right: 0">
            <form wire:submit="sendMessage" class="space-y-4"> 
                <div class="flex">
                    <div class="flex items-center justify-center space-x-4 mt-5 mr-2 cursor-pointer">
                        <button id="recordButton" class="bg-red-500 text-white px-4 py-2 rounded-lg" type="button">
                            🎤
                        </button>
                    </div>
                    <div id="inputContainer" class="flex-1">
                        <x-input label="Message" placeholder="Tell us more information" wire:model="message" />
                    </div>
                    <div id="timerVisualizerContainer" style="display: none;">
                        <span id="timer" class="text-white"></span>
                        <canvas id="visualizer" class="w-full h-12 bg-gray-800 mt-4"></canvas>
                    </div>
                </div>
                
                <x-button white label="Send Message" type="submit" full />
            </form>
        </div>
    @else
        <div class="bg-gray-700 p-8 absolute" style="bottom: 0; left:0; right: 0">
            <x-button white label="Finish Meeting" href="/ideas/{{$meeting->idea_id}}" full />
        </div>
    @endif
</div>

<script>
    function scrollToBottom() {
        let container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    document.addEventListener("DOMContentLoaded", function () {
        let recordButton = document.getElementById("recordButton");
        let timerElement = document.getElementById("timer");
        let visualizer = document.getElementById("visualizer");
        let mediaRecorder;
        let audioChunks = [];
        let timer;
        let seconds = 0;

        // Función para pedir acceso al micrófono
        async function requestMicrophoneAccess() {
            try {
                await navigator.mediaDevices.getUserMedia({ audio: true });
                console.log("Acceso al micrófono concedido");
            } catch (error) {
                console.error("Acceso al micrófono denegado", error);
                alert("Necesitas permitir el acceso al micrófono para grabar audio.");
            }
        }

        function startTimer() {
            timer = setInterval(() => {
                seconds++;
                timerElement.innerText = `⏳ ${seconds} sec`;
            }, 1000);
        }

        function stopTimer() {
            clearInterval(timer);
            timerElement.innerText = "";
            seconds = 0;
        }

        async function startRecording() {
            try {
                let stream = await navigator.mediaDevices.getUserMedia({ audio: true });

                mediaRecorder = new MediaRecorder(stream);
                let audioContext = new AudioContext();
                let analyser = audioContext.createAnalyser();
                let source = audioContext.createMediaStreamSource(stream);
                source.connect(analyser);

                let canvasContext = visualizer.getContext("2d");
                analyser.fftSize = 256;
                let bufferLength = analyser.frequencyBinCount;
                let dataArray = new Uint8Array(bufferLength);

                function draw() {
                    requestAnimationFrame(draw);
                    analyser.getByteFrequencyData(dataArray);
                    canvasContext.clearRect(0, 0, visualizer.width, visualizer.height);
                    canvasContext.fillStyle = "#4caf50";
                    let barWidth = (visualizer.width / bufferLength) * 2.5;
                    let x = 0;

                    for (let i = 0; i < bufferLength; i++) {
                        let barHeight = dataArray[i] / 2;
                        canvasContext.fillRect(x, visualizer.height - barHeight / 2, barWidth, barHeight);
                        x += barWidth + 1;
                    }
                }

                draw();

                mediaRecorder.ondataavailable = event => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = async () => {
                    stopTimer();
                    stream.getTracks().forEach(track => track.stop());

                    let audioBlob = new Blob(audioChunks, { type: "audio/wav" });
                    let formData = new FormData();
                    formData.append("audioFile", audioBlob);

                    @this.upload('audioFile', audioBlob, () => {
                        @this.call('saveAudio');
                    });

                    audioChunks = [];
                };

                mediaRecorder.start();
                startTimer();
            } catch (error) {
                console.error("Error al acceder al micrófono:", error);
                alert("Error al acceder al micrófono. Asegúrate de haber dado permiso.");
            }
        }

        function stopRecording() {
            if (mediaRecorder && mediaRecorder.state !== "inactive") {
                mediaRecorder.stop();
            }
        }

        // Pedir permiso al cargar la página
        requestMicrophoneAccess();

        // Eventos de grabación
        recordButton.addEventListener("mousedown", startRecording);
        recordButton.addEventListener("mouseup", stopRecording);
    });
</script>
@script
    <script>
        Livewire.hook('request', ({ component, cleanup }) => {
            scrollToBottom();
        });

        Livewire.hook('component.init', ({ component, cleanup }) => {
            scrollToBottom();
        });
        
    </script>
@endscript