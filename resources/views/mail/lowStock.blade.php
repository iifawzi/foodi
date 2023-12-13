@component("mail::message")

Hi {{ $name }} Restaurant's Supervisor.
we need to inform you that the stocks below have exceeded their defined limit.

@component('mail::table')
| Ingredient ID | Ingredient Name | Current | Threshold |
| :------------: | :--------------: | :------: | :--------: |
@foreach ($items as $item)
| {{ $item['ingredientId'] }} | {{ $item['ingredientName'] }} | {{ $item['current'] }} | {{ $item['threshold'] }} |
@endforeach
@endcomponent

Thanks, <br>
{{ config('app.name') }}

@endcomponent
