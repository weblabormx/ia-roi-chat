<div x-data="liveMeeting" x-init="init()" class="max-w-5xl mx-auto relative h-full">
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
                        <button @mousedown="startRecording" @mouseup="stopRecording" class="bg-red-500 text-white px-4 py-2 rounded-lg" type="button">
                            <span x-text="isRecording ? '🔴' : '🎤'"></span>
                        </button>
                    </div>
                    <div id="inputContainer" class="flex-1" x-show="!isRecording">
                        <x-input label="Message" placeholder="Tell us more information" wire:model="message" />
                    </div>
                    <div id="timerVisualizerContainer" class="mt-5" x-show="isRecording">
                        <span id="timer" class="text-white w-32 pt-2" x-text="timerText"></span>
                        <canvas id="visualizer" class="w-full h-10 bg-gray-800"></canvas>
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('liveMeeting', () => ({
            isRecording: false,
            timerText: '',
            mediaRecorder: null,
            audioChunks: [],
            timer: null,
            seconds: 0,

            init() {
                this.requestMicrophoneAccess();
                this.setupHooks();
            },

            requestMicrophoneAccess() {
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(() => console.log("Micrófono accesible"))
                    .catch(() => alert("Necesitas permitir el acceso al micrófono para grabar audio."));
            },

            setupHooks() {
                Livewire.hook('request', this.scrollToBottom);
                Livewire.hook('component.init', this.scrollToBottom);
            },

            startTimer() {
                this.timer = setInterval(() => {
                    this.seconds++;
                    this.timerText = `⏳ ${this.seconds} sec`;
                }, 1000);
            },

            stopTimer() {
                clearInterval(this.timer);
                this.timerText = '';
                this.seconds = 0;
            },

            async startRecording() {
                this.isRecording = true;
                let stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);

                let audioContext = new AudioContext();
                let analyser = audioContext.createAnalyser();
                let source = audioContext.createMediaStreamSource(stream);
                source.connect(analyser);

                let canvas = document.getElementById("visualizer");
                let canvasContext = canvas.getContext("2d");
                analyser.fftSize = 256;
                let bufferLength = analyser.frequencyBinCount;
                let dataArray = new Uint8Array(bufferLength);

                const drawVisualizer = () => {
                    requestAnimationFrame(drawVisualizer);
                    analyser.getByteFrequencyData(dataArray);
                    canvasContext.clearRect(0, 0, canvas.width, canvas.height);
                    canvasContext.fillStyle = "#4caf50";
                    let barWidth = (canvas.width / bufferLength) * 2.5;
                    let x = 0;

                    for (let i = 0; i < bufferLength; i++) {
                        let barHeight = dataArray[i] / 2;
                        canvasContext.fillRect(x, canvas.height - barHeight / 2, barWidth, barHeight);
                        x += barWidth + 1;
                    }
                };

                drawVisualizer();

                this.mediaRecorder.ondataavailable = event => {
                    this.audioChunks.push(event.data);
                };

                this.mediaRecorder.onstop = async () => {
                    this.stopTimer();
                    stream.getTracks().forEach(track => track.stop());
                    
                    let audioBlob = new Blob(this.audioChunks, { type: "audio/wav" });
                    @this.upload('audioFile', audioBlob, () => {
                        @this.call('saveAudio');
                    });

                    this.audioChunks = [];
                };

                this.mediaRecorder.start();
                this.startTimer();
            },

            stopRecording() {
                if (this.mediaRecorder && this.mediaRecorder.state !== "inactive") {
                    this.mediaRecorder.stop();
                    this.isRecording = false;
                }
            },

            scrollToBottom() {
                let container = document.getElementById('messagesContainer');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            },
        }));
    });
</script>
