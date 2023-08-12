<div x-data="{
    timer: {
        days: '{{ $this->days() }}',
        hours: '{{ $this->hours() }}',
        minutes: '{{ $this->minutes() }}',
        seconds: '{{ $this->seconds() }}',
        show: new Date({{ $targetDateTime->timestamp }} * 1000).getTime() - new Date().getTime() >= 0,
    },
    error: '{{ $hasError }}',
    startCounter: function() {
        let runningCounter = setInterval(() => {
            let countDownDate = new Date({{ $targetDateTime->timestamp }} * 1000).getTime();
            let timeDistance = countDownDate - new Date().getTime();

            if (timeDistance < 0) {
                clearInterval(runningCounter);
                this.timer.show = false
                return;
            }

            this.timer.days = this.formatCounter(Math.floor(timeDistance / (1000 * 60 * 60 * 24)));
            this.timer.hours = this.formatCounter(Math.floor((timeDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
            this.timer.minutes = this.formatCounter(Math.floor((timeDistance % (1000 * 60 * 60)) / (1000 * 60)));
            this.timer.seconds = this.formatCounter(Math.floor((timeDistance % (1000 * 60)) / 1000));
        }, 1000);
    },
    formatCounter: function(number) {
        return number.toString().padStart(2, '0');
    }
}" x-init="startCounter()">
    <div x-show="false">
        Loading...
    </div>
    <div x-cloak x-show="error" class='relative'>
        {{ $errorText }}
    </div>
    <div>
        <div x-cloak x-show="timer.show && !error">
            {{ $counterText }}
            <span x-text="timer.hours">{{ $this->hours() }}</span>:<span
                x-text="timer.minutes">{{ $this->minutes() }}</span>:<span
                x-text="timer.seconds">{{ $this->seconds() }}</span>!
        </div>
    </div>
    <div>
        <div x-cloak x-show="!timer.show && !error" class='relative'>
            {{ $doneText }}
            <x-icons icon='loading' class='inline animate-spin' />
        </div>
    </div>
</div>
