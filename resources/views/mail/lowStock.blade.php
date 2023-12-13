@component("mail::message")

Hi {{ $name }} Restaurant's Supervisor.
we need to inform you that the stocks below have exceeded their defined limit.

@component('mail::table')
| Ingredient ID | Ingredient Name | Current | Threshold |
| :------------: | :--------------: | :------: | :--------: |
| {{ $item['ingredient_id'] }} | {{ $item['ingredient_name'] }} | {{ $item['current'] }} | {{ $item['threshold'] }} |
@endcomponent

Thanks, <br>
{{ config('app.name') }}

@endcomponent
