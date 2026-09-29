<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

    {{-- フォームタイトル --}}
    <h2 class="text-xl font-bold text-blue-900">
        新規予約
    </h2>

    <p class="mt-1 text-sm text-slate-500">
        新しい予約情報を入力してください。
    </p>


    {{-- 登録成功メッセージ --}}
    @if (session()->has('message'))
        <div class="mt-4 rounded border border-green-200 bg-green-50 p-3 text-green-700">
            {{ session('message') }}
        </div>
    @endif


    {{--
        wire:submit="save"

        フォームを送信すると、
        ReservationForm.php の save() が実行されます。
    --}}
    <form wire:submit="save" class="mt-5">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

            {{-- 予約者名 --}}
            <div>
                <label class="mb-1 block text-sm font-bold">
                    予約者名
                </label>

                <input
                    type="text"
                    wire:model="name"
                    placeholder="例：山田太郎"
                    class="w-full rounded border border-slate-300 px-3 py-2"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- 人数 --}}
            <div>
                <label class="mb-1 block text-sm font-bold">
                    人数
                </label>

                <input
                    type="number"
                    wire:model="people"
                    min="1"
                    class="w-full rounded border border-slate-300 px-3 py-2"
                >

                @error('people')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- 席 --}}
            <div>
                <label class="mb-1 block text-sm font-bold">
                    席
                </label>

                <select
                    wire:model="seat"
                    class="w-full rounded border border-slate-300 px-3 py-2"
                >
                    <option value="">
                        席を選択してください
                    </option>

                    @foreach ($seats as $seatOption)
                        <option value="{{ $seatOption }}">
                            {{ $seatOption }}
                        </option>
                    @endforeach
                </select>

                @error('seat')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- 開始時間 --}}
            <div>
                <label class="mb-1 block text-sm font-bold">
                    開始時間
                </label>

                <select
                    wire:model="startTime"
                    class="w-full rounded border border-slate-300 px-3 py-2"
                >
                    <option value="">
                        開始時間を選択してください
                    </option>

                    @foreach ($times as $time)
                        <option value="{{ $time }}">
                            {{ $time }}
                        </option>
                    @endforeach
                </select>

                @error('startTime')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- 終了時間 --}}
            <div>
                <label class="mb-1 block text-sm font-bold">
                    終了時間
                </label>

                <select
                    wire:model="endTime"
                    class="w-full rounded border border-slate-300 px-3 py-2"
                >
                    <option value="">
                        終了時間を選択してください
                    </option>

                    @foreach ($times as $time)
                        <option value="{{ $time }}">
                            {{ $time }}
                        </option>
                    @endforeach
                </select>

                @error('endTime')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>


        {{-- 登録ボタン --}}
        <div class="mt-6 flex justify-end">

            <button
                type="submit"
                class="rounded bg-blue-900 px-6 py-2 font-bold text-white hover:bg-blue-800"
            >
                予約を登録
            </button>

        </div>

    </form>

</div>