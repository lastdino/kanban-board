<div>
    <div class="flex flex-col gap-4 mt-2">
        @if($this->project->users->where('id', auth()->id())->first())
            <div class="space-y-6" x-data="dragdrop">
                <div
                    x-on:dragover.prevent="dragging = true"
                    x-on:dragleave="dragging = false"
                    x-on:drop.prevent="handleDrop($event)"
                    :class="{ 'bg-lime-500': dragging }"
                >
                    <div class="border border-dashed border-gray-400 h-32 flex flex-col items-center justify-center"
                         x-on:click="$refs.fileInput.click()"
                    >
                        <flux:text>ドラッグ＆ドロップしてファイルをアップロード</flux:text>
                        <flux:text>クリックでファイル選択</flux:text>

                    </div>
                    <input type="file"  multiple x-on:change="handleFileInput($event)" x-ref="fileInput" style="display:none">
                    @error('files.*')
                    <span class="block sm:inline text-red-700">{{ $message }}</span>
                    @enderror
                </div>
                <flux:separator />
                <div x-show="isUploading"  class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                    <div class="bg-blue-500 h-full transition-all duration-300 rounded-full" :style="`width: ${progress}%`"></div>
                </div>
            </div>
        @endif
        <div class="grid grid-cols-1 gap-4">
            @foreach($files as $file)
                <flux:callout icon="document">
                    <flux:callout.heading class="flex justify-between">
                        <div>
                            {{$file->name}}
                        </div>
                        <div class="">
                            <flux:dropdown>
                                <flux:button icon="ellipsis-horizontal" size="sm" variant="subtle" :disabled="!$this->project->users->where('id', auth()->id())->first()"></flux:button>
                                <flux:menu>
                                    <flux:menu.item icon="arrow-down-tray" href="{{$this->temporaryURL($file->id)}}">
                                        ダウンロード
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item variant="danger" icon="trash" wire:click="delete({{$file->id}})" wire:confirm="ファイルを削除してもよろしいですか">削除</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </flux:callout.heading>
                    <flux:callout.text>
                        アップロード日：{{$file->created_at}}
                    </flux:callout.text>
                </flux:callout>
            @endforeach
        </div>
    </div>
</div>
@script
<script>
    Alpine.data('dragdrop', () => ({
        files: [],
        dragging: false,
        isUploading: false,
        isComplete: false,
        progress: 0,
        init(){
        },
        handleDrop(e) {
            this.dragging = false;
            if (e.dataTransfer.files.length > 0) {
                const files = e.dataTransfer.files;
                Array.from(files).map(f => {
                    this.files.push(f);
                })
            }
            this.send();
            //console.log(this.files);
        },
        handleFileInput(e) {
            const files = e.target.files;
            Array.from(files).forEach(file => {
                this.files.push(file);
            });
            this.send();
            //console.log(this.files);
        },
        send(){
            //console.log(this.files);
            this.isUploading = true
            this.isComplete = false;
            //console.log(this.isUploading);
            $wire.uploadMultiple(
                'up_files',
                this.files,
                (uploadedFilename) => {
                    //console.log('f');
                    this.isUploading = false
                    this.isComplete = true;
                    //console.log(this.isUploading);
                    this.progress = 100
                    $wire.dispatch('uploaded-file');
                }, () => {
                    console.log('error');
                }, (event) => {
                    //console.log(event)
                    this.progress = event.detail.progress
                }, () => {
                    // Cancelled callback...
                    console.log('Cancel', )
                }
            )
        },

    }))
</script>
@endscript
