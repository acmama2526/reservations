<div class="p-4">

<h2>店舗設定</h2>

<div>
<label>店舗名</label>
<input
type="text"
wire:model="shop_name"
>
</div>

<div>
<label>営業時間</label>

<input
type="time"
wire:model="business_start"
>

<span>〜</span>

<input
type="time"
wire:model="business_end"
>
</div>

<div>
<label>表示単位</label>

<select wire:model="slot_minutes">
<option value="15">15分</option>
<option value="30">30分</option>
<option value="60">60分</option>
</select>
</div>

<div>
<label>定休日</label>

<label>
<input type="checkbox" value="月曜日" wire:model="closed_days">
月曜日
</label>

<label>
<input type="checkbox" value="火曜日" wire:model="closed_days">
火曜日
</label>

<label>
<input type="checkbox" value="水曜日" wire:model="closed_days">
水曜日
</label>

<label>
<input type="checkbox" value="木曜日" wire:model="closed_days">
木曜日
</label>

<label>
<input type="checkbox" value="金曜日" wire:model="closed_days">
金曜日
</label>

<label>
<input type="checkbox" value="土曜日" wire:model="closed_days">
土曜日
</label>

<label>
<input type="checkbox" value="日曜日" wire:model="closed_days">
日曜日
</label>

<label>
<input type="checkbox" value="祝日" wire:model="closed_days">
祝日
</label>
</div>

<div>
<button wire:click="save">
保存
</button>
</div>

</div>