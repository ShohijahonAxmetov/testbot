@extends('layouts.client')
@section('title', $test->id.' - '.$test->name)
@section('content')
<div class="container my-5">
	<h1 class="mb-4">Test natijalari. ({{$test->id}} - {{$test->name}})</h1>
	<p class="m-0 mb-1 p-0"><b>Test boshlangan vaqt: </b><span class="bg-success text-light">{{date('d-m-Y H:i', strtotime($test->start_date))}}</span></p>
	<p class="m-0 p-0 mb-4"><b>Test yakunlangan vaqt: </b><span class="bg-danger text-light">{{date('d-m-Y H:i', strtotime($test->end_date))}}</span></p>
	<table class="table table-striped">
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col">To'liq ism</th>
				<th scope="col">To'g'ri javoblar</th>
				<th scope="col">Noto'g'ri javoblar</th>
				<th scope="col">Topshirilgan vaqti</th>
			</tr>
		</thead>
		<tbody class="table-group-divider">
			@foreach($userAnswers as $userAnswer)
			<tr>
				<th scope="row">{{$loop->iteration}}</th>
				<td>{{$userAnswer->telegramUser->name}}</td>
				<td>{{$userAnswer->points}}</td>
				<td>{{strlen($userAnswer->test->answers) - $userAnswer->points}}</td>
				<td>{{date('d-m-Y H:i', strtotime($userAnswer->created_at))}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection