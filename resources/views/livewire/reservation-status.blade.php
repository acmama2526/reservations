<div class="w-full" x-data="reservationTimeline({
    businessStart: '{{ $businessStart }}',
    businessEnd: '{{ $businessEnd }}',
    businessMinutes: {{ $businessMinutes }},
    slotMinutes: {{ $slotMinutes }}
})">

    {{-- ================================================================
         予約状況ヘッダー
    ================================================================= --}}
    <div
        class="
            mb-3
            flex
            flex-col
            gap-3

            sm:flex-row
            sm:items-center
            sm:justify-between
        ">

        {{-- 日付変更 --}}
        <div
            class="
                flex
                flex-wrap
                items-center
                gap-2
            ">

            {{-- 前日 --}}
            <button type="button" wire:click="previousDay"
                class="
                    flex
                    h-9
                    w-9
                    items-center
                    justify-center
                    rounded-lg
                    border
                    border-slate-300
                    bg-white
                    transition

                    hover:bg-slate-50
                ">
                ←
            </button>


            {{-- 日付カレンダー --}}
            <input type="date" wire:model.live="date"
                class="
                    h-9
                    rounded-lg
                    border-slate-300
                    text-sm
                ">


            {{-- 翌日 --}}
            <button type="button" wire:click="nextDay"
                class="
                    flex
                    h-9
                    w-9
                    items-center
                    justify-center
                    rounded-lg
                    border
                    border-slate-300
                    bg-white
                    transition

                    hover:bg-slate-50
                ">
                →
            </button>


            {{-- 表示単位 --}}
            <select wire:model.live="displayMinutes"
                class="
                    h-9
                    rounded-lg
                    border-slate-300
                    text-sm
                ">

                <option value="15">
                    15分表示
                </option>

                <option value="30">
                    30分表示
                </option>

                <option value="60">
                    1時間表示
                </option>

            </select>

        </div>

        {{-- 新規予約ボタン --}}
        <button type="button" wire:click="$parent.openCreateModalFromButton"
            class="
                    inline-flex
                    items-center
                    gap-1
                    rounded-lg
                    bg-blue-700
                    px-4 py-2
                    text-sm
                    font-semibold
                    text-white
                    shadow-sm
                    transition

                    hover:bg-blue-800

                    focus:outline-none
                    focus:ring-2
                    focus:ring-blue-300
                ">

            <span class="text-lg leading-none">
                ＋
            </span>

            新規予約

        </button>

    </div>


    {{-- ================================================================
         予約Timeline

         overflow-autoを使用しません。

         予約状況そのものが縦に長くなった場合は
         ブラウザ全体がスクロールします。
    ================================================================= --}}
    <div
        class="
            w-full
            overflow-hidden
            rounded-lg
            border
            border-slate-200
            bg-white
        ">

        {{-- ============================================================
             時間ヘッダー
        ============================================================= --}}
        <div
            class="
                flex
                h-10
                border-b
                border-slate-200
                bg-slate-100
            ">

            {{-- 席列 --}}
            <div
                class="
                    flex
                    w-24
                    shrink-0
                    items-center
                    justify-center
                    border-r
                    border-slate-200
                    text-xs
                    font-bold
                    text-slate-700
                ">
                席
            </div>


            {{-- 時間Timeline --}}
            <div class="relative flex-1">

                @foreach ($times as $time)
                    @php

                        /*
                         * 営業開始時間
                         */
                        $headerStart = \Carbon\Carbon::createFromFormat('H:i', $businessStart);

                        /*
                         * 現在の時間目盛り
                         */
                        $headerTime = \Carbon\Carbon::createFromFormat('H:i', $time);

                        /*
                         * 営業開始から何分後か
                         */
                        $minutes = $headerStart->diffInMinutes($headerTime, false);

                        /*
                         * Timeline上の位置
                         */
                        $position = $businessMinutes > 0 ? ($minutes / $businessMinutes) * 100 : 0;

                    @endphp


                    <div class="
                            pointer-events-none
                            absolute
                            top-0
                            h-full
                            border-l
                            border-slate-300
                        "
                        style="
                            left: {{ $position }}%;
                        ">

                        <span
                            class="
                                absolute
                                left-1
                                top-2
                                whitespace-nowrap
                                text-[10px]
                                text-slate-500
                            ">

                            {{ $time }}

                        </span>

                    </div>
                @endforeach

            </div>

        </div>


        {{-- ============================================================
             席
        ============================================================= --}}
        @foreach ($seats as $seat)
            <div
                class="
                    flex
                    h-12
                    border-b
                    border-slate-200

                    last:border-b-0
                ">

                {{-- ====================================================
                     席名
                ===================================================== --}}
                <div
                    class="
                        flex
                        w-24
                        shrink-0
                        items-center
                        justify-center
                        border-r
                        border-slate-200
                        bg-slate-50
                        px-1
                        text-center
                        text-xs
                        font-bold
                    ">

                    {{ $seat->seat_name }}

                </div>


                {{-- ====================================================
                     Timeline Row
                ===================================================== --}}
                <div class="
                        timeline-row
                        relative
                        flex-1
                        bg-white
                    "
                    data-seat-id="{{ $seat->id }}">

                    {{-- =================================================
                         空き枠クリック専用レイヤー

                         これが今回の重要な修正です。

                         Timeline全体に透明buttonを敷きます。

                         予約ブロックはz-20なので、
                         予約部分ではこちらではなく
                         予約ブロック側が操作されます。
                    ================================================== --}}
                    <button type="button"
                        class="
                            absolute
                            inset-0
                            z-0
                            block
                            h-full
                            w-full
                            cursor-pointer
                            border-0
                            bg-transparent
                            p-0

                            hover:bg-blue-50/30
                        "
                        title="クリックして新規予約"
                        @click="
                            emptyClick(
                                $event,
                                {{ $seat->id }}
                            )
                        "></button>


                    {{-- =================================================
                         時間Grid線
                    ================================================== --}}
                    @foreach ($times as $time)
                        @php

                            $gridStart = \Carbon\Carbon::createFromFormat('H:i', $businessStart);

                            $gridTime = \Carbon\Carbon::createFromFormat('H:i', $time);

                            $minutes = $gridStart->diffInMinutes($gridTime, false);

                            $position = $businessMinutes > 0 ? ($minutes / $businessMinutes) * 100 : 0;

                        @endphp


                        <div class="
                                pointer-events-none
                                absolute
                                inset-y-0
                                z-[1]
                                border-l
                                border-slate-200
                            "
                            style="
                                left: {{ $position }}%;
                            ">
                        </div>
                    @endforeach


                    {{-- =================================================
                         この席の予約ブロック
                    ================================================== --}}
                    @foreach ($reservations as $reservation)
                        @if ((int) $reservation['seat_id'] === (int) $seat->id)
                            <div wire:key="
                                    reservation-block-
                                    {{ $reservation['id'] }}-
                                    {{ $seat->id }}-
                                    {{ $reservation['start_time'] }}-
                                    {{ $reservation['end_time'] }}
                                "
                                class="
                                    reservation-block
                                    absolute
                                    bottom-1
                                    top-1
                                    z-20
                                    cursor-grab
                                    select-none
                                    overflow-hidden
                                    rounded-md
                                    border
                                    border-blue-300
                                    bg-blue-100
                                    shadow-sm

                                    active:cursor-grabbing
                                "
                                style="
                                    left:
                                    {{ $reservation['left'] }}%;

                                    width:
                                    {{ $reservation['width'] }}%;
                                "
                                data-id="{{ $reservation['id'] }}" data-seat-id="{{ $reservation['seat_id'] }}"
                                data-start="{{ $reservation['start_time'] }}"
                                data-end="{{ $reservation['end_time'] }}"
                                @pointerdown="
                                    beginMove(
                                        $event,
                                        $el
                                    )
                                "
                                @click.stop="
                                    reservationClick(
                                        {{ $reservation['id'] }}
                                    )
                                ">

                                {{-- =========================================
                                     左側リサイズ
                                ========================================== --}}
                                <div class="
                                        absolute
                                        bottom-0
                                        left-0
                                        top-0
                                        z-30
                                        w-2
                                        cursor-ew-resize

                                        hover:bg-blue-500/30
                                    "
                                    title="開始時間を変更"
                                    @pointerdown.stop="
                                        beginResize(
                                            $event,
                                            $el.parentElement,
                                            'start'
                                        )
                                    ">
                                </div>


                                {{-- =========================================
                                     予約内容
                                ========================================== --}}
                                <div
                                    class="
                                        pointer-events-none
                                        h-full
                                        overflow-hidden
                                        px-3
                                        py-1
                                    ">

                                    <div
                                        class="
                                            truncate
                                            text-xs
                                            font-bold
                                            text-blue-900
                                        ">

                                        {{ $reservation['name'] }}

                                    </div>


                                    <div
                                        class="
                                            whitespace-nowrap
                                            text-[10px]
                                            text-blue-700
                                        ">

                                        {{ $reservation['start_time'] }}

                                        ～

                                        {{ $reservation['end_time'] }}

                                        ・

                                        {{ $reservation['people'] }}名

                                    </div>

                                </div>


                                {{-- =========================================
                                     右側リサイズ
                                ========================================== --}}
                                <div class="
                                        absolute
                                        bottom-0
                                        right-0
                                        top-0
                                        z-30
                                        w-2
                                        cursor-ew-resize

                                        hover:bg-blue-500/30
                                    "
                                    title="終了時間を変更"
                                    @pointerdown.stop="
                                        beginResize(
                                            $event,
                                            $el.parentElement,
                                            'end'
                                        )
                                    ">
                                </div>

                            </div>
                        @endif
                    @endforeach

                </div>

            </div>
        @endforeach

    </div>


    {{-- ================================================================
         Alpine.js

         ・空き枠クリック
         ・予約クリック
         ・ドラッグ
         ・左右リサイズ
    ================================================================= --}}
    <script>
        document.addEventListener(
            'alpine:init',

            () => {

                Alpine.data(
                    'reservationTimeline',

                    (config) => ({

                        /*
                        |--------------------------------------------------------------------------
                        | 操作状態
                        |--------------------------------------------------------------------------
                        */
                        active: null,

                        saving: false,
                        stopFailureListener: null,

                        dragged: false,

                        pointerMove: null,

                        pointerUp: null,


                        /*
                        |--------------------------------------------------------------------------
                        | HH:mm → 分
                        |--------------------------------------------------------------------------
                        */
                        timeToMinutes(time) {

                            const [
                                hour,
                                minute
                            ] =
                            time
                                .split(':')
                                .map(Number);


                            return (
                                hour * 60 +
                                minute
                            );
                        },


                        /*
                        |--------------------------------------------------------------------------
                        | 分 → HH:mm
                        |--------------------------------------------------------------------------
                        */
                        minutesToTime(minutes) {

                            minutes =
                                Math.round(minutes);


                            const hour =
                                Math.floor(
                                    minutes / 60
                                );


                            const minute =
                                minutes % 60;


                            return (
                                String(hour)
                                .padStart(
                                    2,
                                    '0'
                                ) +
                                ':' +
                                String(minute)
                                .padStart(
                                    2,
                                    '0'
                                )
                            );
                        },


                        /*
                        |--------------------------------------------------------------------------
                        | ドラッグ単位へ丸める
                        |--------------------------------------------------------------------------
                        */
                        snap(minutes) {

                            return (
                                Math.round(
                                    minutes /
                                    config.slotMinutes
                                ) *
                                config.slotMinutes
                            );
                        },


                        /*
                        |--------------------------------------------------------------------------
                        | 空き枠クリック
                        |--------------------------------------------------------------------------
                        |
                        | 新規予約については15分単位で選択します。
                        |
                        | 表示単位が
                        |
                        | 30分
                        | 60分
                        |
                        | でも15分単位でクリックできます。
                        |
                        */
                        emptyClick(
                            event,
                            seatId
                        ) {

                            /*
                             * 空き枠buttonの位置
                             */
                            const rect =
                                event.currentTarget
                                .getBoundingClientRect();


                            /*
                             * クリック位置
                             */
                            const clickedX =
                                event.clientX -
                                rect.left;


                            /*
                             * Timeline上の割合
                             */
                            const ratio =
                                clickedX /
                                rect.width;


                            /*
                             * 営業開始
                             */
                            const businessStart =
                                this.timeToMinutes(
                                    config.businessStart
                                );


                            /*
                             * 営業終了
                             */
                            const businessEnd =
                                this.timeToMinutes(
                                    config.businessEnd
                                );


                            /*
                             * クリック位置を
                             * 分へ変換
                             */
                            let minutes =
                                businessStart +
                                (
                                    ratio *
                                    config.businessMinutes
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | 新規予約は15分刻み
                            |--------------------------------------------------------------------------
                            */
                            minutes =
                                Math.round(
                                    minutes / 15
                                ) *
                                15;


                            /*
                             * 営業時間内へ制限
                             */
                            minutes =
                                Math.max(
                                    businessStart,

                                    Math.min(
                                        minutes,
                                        businessEnd - 15
                                    )
                                );


                            /*
                             * Livewireへ送信
                             */
                            this.$wire.selectEmptySlot(
                                seatId,

                                this.minutesToTime(
                                    minutes
                                )
                            );
                        },


                        /*
                        |--------------------------------------------------------------------------
                        | 予約クリック
                        |--------------------------------------------------------------------------
                        */
                        reservationClick(id) {

                            /*
                             * ドラッグ後のclickなら
                             * 編集モーダルを開きません。
                             */
                            if (this.dragged || this.saving) {
                                return;
                            }


                            this.$wire.selectReservation(
                                id
                            );
                        },


                        /*
                        |--------------------------------------------------------------------------
                        | 移動開始
                        |--------------------------------------------------------------------------
                        */
                        beginMove(
                            event,
                            element
                        ) {

                            /*
                             * 左クリックのみ
                             */
                            if (
                                event.button !== 0 || this.saving
                            ) {
                                return;
                            }


                            if (this.saving) return;
                            event.preventDefault();


                            const row =
                                element.closest(
                                    '.timeline-row'
                                );


                            if (!row) {
                                return;
                            }


                            const rect =
                                row.getBoundingClientRect();


                            this.active = {

                                mode: 'move',

                                element: element,

                                reservationId: Number(
                                    element.dataset.id
                                ),

                                originalSeatId: Number(
                                    element.dataset.seatId
                                ),

                                startX: event.clientX,

                                originalLeft: parseFloat(
                                    element.style.left
                                ),

                                originalWidth: parseFloat(
                                    element.style.width
                                ),

                                rowWidth: rect.width,
                            };


                            this.dragged = false;


                            window.addEventListener(
                                'pointermove',
                                this.pointerMove
                            );


                            window.addEventListener(
                                'pointerup',
                                this.pointerUp, {
                                    once: true
                                }
                            );
                        },


                        /*
                        |--------------------------------------------------------------------------
                        | リサイズ開始
                        |--------------------------------------------------------------------------
                        */
                        beginResize(
                            event,
                            element,
                            side
                        ) {

                            if (this.saving) return;
                            event.preventDefault();


                            const row =
                                element.closest(
                                    '.timeline-row'
                                );


                            if (!row) {
                                return;
                            }


                            const rect =
                                row.getBoundingClientRect();


                            this.active = {

                                mode: side === 'start' ?
                                    'resize-start' : 'resize-end',

                                element: element,

                                reservationId: Number(
                                    element.dataset.id
                                ),

                                originalSeatId: Number(
                                    element.dataset.seatId
                                ),

                                startX: event.clientX,

                                originalLeft: parseFloat(
                                    element.style.left
                                ),

                                originalWidth: parseFloat(
                                    element.style.width
                                ),

                                rowWidth: rect.width,
                            };


                            this.dragged = false;


                            window.addEventListener(
                                'pointermove',
                                this.pointerMove
                            );


                            window.addEventListener(
                                'pointerup',
                                this.pointerUp, {
                                    once: true
                                }
                            );
                        },


                        /*
                        |--------------------------------------------------------------------------
                        | Alpine初期化
                        |--------------------------------------------------------------------------
                        */
                        destroy() {
                            window.removeEventListener('pointermove', this.pointerMove);
                            window.removeEventListener('pointerup', this.pointerUp);
                            this.stopFailureListener?.();
                        },

                        init() {
                            this.stopFailureListener = this.$wire.on('reservation-drag-failed', (event) => {
                                window.alert(event.message ?? event.detail?.message ??
                                    '予約を更新できませんでした。');
                            });

                            /*
                            |--------------------------------------------------------------------------
                            | pointermove
                            |--------------------------------------------------------------------------
                            */
                            this.pointerMove =
                                (event) => {

                                    if (!this.active) {
                                        return;
                                    }


                                    /*
                                     * マウス移動量
                                     */
                                    const deltaPixels =
                                        event.clientX -
                                        this.active.startX;


                                    /*
                                     * 少しでも動いた場合だけ
                                     * ドラッグとして扱います。
                                     */
                                    if (
                                        Math.abs(
                                            deltaPixels
                                        ) >
                                        3
                                    ) {
                                        this.dragged = true;
                                    }


                                    /*
                                     * px → %
                                     */
                                    const deltaPercent =
                                        (
                                            deltaPixels /
                                            this.active.rowWidth
                                        ) *
                                        100;


                                    /*
                                     * 1slot分の%
                                     */
                                    const slotPercent =
                                        (
                                            config.slotMinutes /
                                            config.businessMinutes
                                        ) *
                                        100;


                                    /*
                                     * slot単位へスナップ
                                     */
                                    const snappedDelta =
                                        Math.round(
                                            deltaPercent /
                                            slotPercent
                                        ) *
                                        slotPercent;


                                    /*
                                    |--------------------------------------------------------------------------
                                    | 移動
                                    |--------------------------------------------------------------------------
                                    */
                                    if (
                                        this.active.mode ===
                                        'move'
                                    ) {

                                        let left =
                                            this.active.originalLeft +
                                            snappedDelta;


                                        /*
                                         * Timeline外へ出ないようにする
                                         */
                                        left =
                                            Math.max(
                                                0,

                                                Math.min(
                                                    left,

                                                    100 -
                                                    this.active.originalWidth
                                                )
                                            );


                                        this.active
                                            .element
                                            .style
                                            .left =
                                            `${left}%`;
                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | 左端リサイズ
                                    |--------------------------------------------------------------------------
                                    */
                                    if (
                                        this.active.mode ===
                                        'resize-start'
                                    ) {

                                        let left =
                                            this.active.originalLeft +
                                            snappedDelta;


                                        let width =
                                            this.active.originalWidth -
                                            snappedDelta;


                                        /*
                                         * 最低1slot
                                         */
                                        if (
                                            width >=
                                            slotPercent &&
                                            left >=
                                            0
                                        ) {

                                            this.active
                                                .element
                                                .style
                                                .left =
                                                `${left}%`;


                                            this.active
                                                .element
                                                .style
                                                .width =
                                                `${width}%`;
                                        }
                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | 右端リサイズ
                                    |--------------------------------------------------------------------------
                                    */
                                    if (
                                        this.active.mode ===
                                        'resize-end'
                                    ) {

                                        let width =
                                            this.active.originalWidth +
                                            snappedDelta;


                                        /*
                                         * 最低1slot
                                         *
                                         * 右端100%を超えない
                                         */
                                        if (
                                            width >=
                                            slotPercent &&
                                            (
                                                this.active.originalLeft +
                                                width
                                            ) <=
                                            100
                                        ) {

                                            this.active
                                                .element
                                                .style
                                                .width =
                                                `${width}%`;
                                        }
                                    }
                                };


                            /*
                            |--------------------------------------------------------------------------
                            | pointerup
                            |--------------------------------------------------------------------------
                            */
                            this.pointerUp =
                                (event) => {

                                    /*
                                     * pointermove解除
                                     */
                                    window.removeEventListener(
                                        'pointermove',
                                        this.pointerMove
                                    );


                                    if (!this.active) {
                                        return;
                                    }


                                    /*
                                     * ドラッグしていない
                                     *
                                     * 通常クリックなので
                                     * DB更新しません。
                                     */
                                    if (!this.dragged) {

                                        this.active = null;

                                        return;
                                    }


                                    const element =
                                        this.active.element;


                                    /*
                                    |--------------------------------------------------------------------------
                                    | ドロップ先の席
                                    |--------------------------------------------------------------------------
                                    */
                                    const target =
                                        document.elementFromPoint(
                                            event.clientX,
                                            event.clientY
                                        );


                                    const targetRow =
                                        target
                                        ?.closest(
                                            '.timeline-row'
                                        );


                                    /*
                                     * リサイズの場合は
                                     * 元の席を維持します。
                                     *
                                     * moveの場合のみ
                                     * 別席へ移動できます。
                                     */
                                    let seatId =
                                        this.active.originalSeatId;


                                    if (
                                        this.active.mode ===
                                        'move' &&
                                        targetRow
                                    ) {

                                        seatId =
                                            Number(
                                                targetRow
                                                .dataset
                                                .seatId
                                            );
                                    }


                                    /*
                                     * 営業開始
                                     */
                                    const businessStart =
                                        this.timeToMinutes(
                                            config.businessStart
                                        );


                                    /*
                                     * 現在の左位置
                                     */
                                    const left =
                                        parseFloat(
                                            element.style.left
                                        );


                                    /*
                                     * 現在の幅
                                     */
                                    const width =
                                        parseFloat(
                                            element.style.width
                                        );


                                    /*
                                    |--------------------------------------------------------------------------
                                    | 開始時間
                                    |--------------------------------------------------------------------------
                                    */
                                    let start =
                                        businessStart +
                                        (
                                            left /
                                            100 *
                                            config.businessMinutes
                                        );


                                    /*
                                    |--------------------------------------------------------------------------
                                    | 予約時間
                                    |--------------------------------------------------------------------------
                                    */
                                    let duration =
                                        (
                                            width /
                                            100 *
                                            config.businessMinutes
                                        );


                                    /*
                                     * slot単位へ丸める
                                     */
                                    start =
                                        this.snap(
                                            start
                                        );


                                    duration =
                                        Math.max(
                                            config.slotMinutes,

                                            this.snap(
                                                duration
                                            )
                                        );


                                    /*
                                     * 終了時間
                                     */
                                    let end =
                                        start +
                                        duration;


                                    /*
                                    |--------------------------------------------------------------------------
                                    | 営業時間内へ補正
                                    |--------------------------------------------------------------------------
                                    */
                                    const businessEnd =
                                        this.timeToMinutes(
                                            config.businessEnd
                                        );


                                    if (
                                        end >
                                        businessEnd
                                    ) {

                                        end =
                                            businessEnd;


                                        start =
                                            Math.max(
                                                businessStart,

                                                end -
                                                duration
                                            );
                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | Livewireへ更新依頼
                                    |--------------------------------------------------------------------------
                                    */
                                    const operation = this.active;
                                    // DOMの仮変更を戻してから、DBの結果でLivewireに再描画させます。
                                    element.style.left = `${operation.originalLeft}%`;
                                    element.style.width = `${operation.originalWidth}%`;
                                    this.saving = true;

                                    this.$wire.moveReservation(
                                        operation.reservationId,
                                        seatId,
                                        this.minutesToTime(start),
                                        this.minutesToTime(end),
                                        operation.mode
                                    ).catch((error) => {
                                        console.error('Reservation update failed', error);
                                        window.alert('予約を保存できませんでした。もう一度操作してください。');
                                    }).finally(() => {
                                        this.saving = false;
                                    });

                                    /*
                                     * 操作終了
                                     */
                                    this.active = null;


                                    /*
                                    |--------------------------------------------------------------------------
                                    | click誤発火防止
                                    |--------------------------------------------------------------------------
                                    |
                                    | pointerup直後にclickイベントが
                                    | 発生するため少しだけdraggedを維持します。
                                    |
                                    */
                                    setTimeout(
                                        () => {

                                            this.dragged =
                                                false;

                                        },
                                        100
                                    );
                                };
                        }

                    })
                );

            }
        );
    </script>

</div>
